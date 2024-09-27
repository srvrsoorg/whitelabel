<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Helper;
use App\Enums\UserStatus as UserStatusEnum;
use DB;

class UserController extends Controller
{

    /**
     * Display a paginated list of users with optional search and associated data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $users = User::when(request('search'), function ($query) {
                    $query->where('name', 'like', '%' . request('search') . '%')
                        ->orWhere('email', 'like', '%' . request('search') . '%');
                })
                ->select('id', 'name', 'email', 'avatar', 'email_verified_at', 'credits', 'status', 'country_name', 'country_code', 'created_at')
                ->with('banLockReason')
                ->withCount('servers')
                ->orderByDesc('created_at')
                ->paginate(request()->input('per_page'));

            // ✅ Success response: Return paginated users with their associated data
            return response()->json([
                'users' => $users
            ], 200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Store a newly created user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => ['required', 'string', 'regex:/^[\p{L}\s]+$/u', 'min:3', 'max:255'],
            'email' => 'required|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
            'country_name' => 'required|string',
            'country_code' => 'required|string',
            'region_name' => 'required|string',
            'region_code' => 'required|string',
            'credits' => 'required|numeric|regex:/^-?\d{1,7}(\.\d{0,3})?$/',
            'timezone' => 'required|string',
            'password' => 'required|confirmed|min:8|max:15|regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,15}$/'
        ],[
            'password.regex' => 'The password must contain at least one uppercase letter, one digit, and one special character (@$!%*?&).'
        ]);

        try {
            // Create a new user record in the database
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'avatar' => Helper::gravatar($request->email),
                'status' => UserStatusEnum::PENDING(),
                'country_name' => $request->country_name,
                'country_code' => $request->country_code,
                'region_name' => $request->region_name,
                'region_code' => $request->region_code,
                'credits' => $request->credits,
                'timezone' => $request->timezone,

            ]);

            // Store Activity
            Helper::adminActivity(auth()->user(), 'User', 'Create', 'User (#' . $user->id . ') has been created.');
    
            // ✅ Success response: User created successfully
            return response()->json([
                'message' => 'User created successfully.'
            ], 200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }


    /**
     * Display the details of a specific user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        try {
            // Add a custom attribute to indicate if the user is an admin
            $user['is_admin'] = $user->isSuperAdmin();
            
            // ✅ Success response: User detail
            return response()->json([
                "user" => $user
            ]);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Update the specified user.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        // Validate incoming request data
        $request->validate([
            'name' => ['required', 'string', 'regex:/^[\p{L}\s]+$/u', 'min:3', 'max:255'],
            'email' => 'required|email|max:255|unique:users,email,' . $user->id . 'NULL,id,deleted_at,NULL',
            'email_verified_at' => 'nullable|boolean',
            'credits' => 'required|numeric|regex:/^-?\d{1,7}(\.\d{0,3})?$/',
            'is_admin' => 'required|boolean',
            'country_name' => 'required|string',
            'country_code' => 'required|string',
            'region_name' => 'required|string',
            'region_code' => 'required|string',
            'status' => 'required|in:'. implode(",", UserStatusEnum::values()),
            'timezone' => 'required|string',
            'password' => 'nullable|min:8|max:15|regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,15}$/'
        ],[
            'password.regex' => 'The password must contain at least one uppercase letter, one digit, and one special character (@$!%*?&).'
        ]);

        try {
            // Update user with the validated request data
            $user->name = $request->name;
            $user->email = $request->email;
            $user->avatar = Helper::gravatar($request->email);
            $user->role = $request->is_admin ? "administrator" : "customer";
            $user->credits = $request->credits;
            $user->country_name = $request->country_name;
            $user->country_code = $request->country_code;
            $user->region_name = $request->region_name;
            $user->region_code = $request->region_code;
            $user->status = $request->status;
            $user->timezone = $request->timezone;

            // Update user password if provided
            if($request->password){
                $user->password = bcrypt($request->password);
            }

            // Handle email verification status and update the user status accordingly
            if(is_null($user->email_verified_at) && $request->email_verified_at){
                $user->email_verified_at = now();
                $user->status = 'active'; 
            }elseif(!$request->email_verified_at){
                $user->email_verified_at = null;
            }
            
            // Token revoke
            if(in_array($request->status, [UserStatusEnum::BANNED(), UserStatusEnum::LOCKED()])) {
                $user->tokens()->delete();
            }

            // Save the updated user details  
            $user->save();

            // Store Activity
            Helper::adminActivity(auth()->user(), 'User', 'Update', 'User (#' . $user->id . ') has been updated.');

            // ✅ Success response: User detail updated successfully
            return response()->json([
                "message" => "User detail updated successfully."
            ]);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Update the status of the specified user.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @param string $action The new status to set for the user.
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserStatus(Request $request, User $user, $action)
    {
        // Validate the incoming request data
        $rules = [
            'reason' => 'nullable|string',
        ];

        // Check if the action is valid
        $statuses = UserStatusEnum::values(); // Use UserStatus Enum
        if (!in_array($action, $statuses)) {
            return response()->json([
                "message" => "Invalid Parameter!",
            ], 500);
        }

        // If action is not 'active', make 'reason' required
        if ($action != UserStatusEnum::ACTIVE()) {
            $rules['reason'] = 'required|string';
        }

        $request->validate($rules);

        try {
            // Update or create user's meta to store reason for status change if provided
            if ($action != UserStatusEnum::ACTIVE()) {
                $user->metas()->updateOrCreate([
                    "name" => "account_reason"
                ], [
                    "value" => $request->reason,
                ]);
            }

            // Update user's status
            $user->update(["status" => $action]);

            // Token revoke
            if(in_array($user->status, [UserStatusEnum::BANNED(), UserStatusEnum::LOCKED()])) {
                $user->tokens()->delete();
            }

            // Store Activity
            Helper::adminActivity(auth()->user(), 'User', 'Update', 'User (#' . $user->id . ') changed status to ' . $action . '.');

            // ✅ Success response: User updated successfully
            return response()->json([
                "message" => "User status updated successfully to $action.",
            ], 200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ], 500);
        }
    }

    /**
     * Delete the specified user from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destory(Request $request, User $user)
    {
        try {
            // Check if the user is an administrator
            if($user->isSuperAdmin()) {
                // ❌ Not perform
                return response()->json([
                    'message' => "You cannot delete the admin user!"
                ], 500);
            }

            // Prevent account deletion if the user has active servers.
            if ($user->servers()->exists()) {
                return response()->json([
                    "message" => "Delete user's servers before deleting the account!"
                ], 500);
            }

            // Store user Id before deleting user
            $userId = $user->id;

            // Delete specified user 
            $user->delete();

            // Store Activity
            Helper::adminActivity(auth()->user(), 'User', 'Delete', 'User (#' . $userId . ') has been deleted.');

            // ✅ Success response: User deleted successfully
            return response()->json([
                'message' => "User deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }
}