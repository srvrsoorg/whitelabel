<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class LoginHistoryController extends Controller
{
    /**
     * Get the login history for the given user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(User $user)
    {
        try {
            // Retrieve the list of login records for the authenticated user
            $logins = $user->logins()
                ->select('*')
                ->orderBy('created_at', 'DESC')
                ->paginate(request()->input('per_page'));
            
            // Return the login records as a JSON response
            return response()->json([
                'logins' => $logins
            ], 200);
        } catch (\Exception $e){
            // Return an error response in case of an exception
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }
}
