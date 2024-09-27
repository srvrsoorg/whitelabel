<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\AdminActivity;
use App\Models\User;
use App\Http\Helper;
use App\Interfaces\ActivityRepositoryInterface;

class ActivityController extends Controller
{
        /**
     * The activity repository instance.
     *
     * @var ActivityRepositoryInterface
     */
    private $activityRepository;

    /**
     * Create a new controller instance.
     *
     * @param ActivityRepositoryInterface $activityRepository
     * @return void
     */
    public function __construct(ActivityRepositoryInterface $activityRepository) 
    {
        $this->activityRepository = $activityRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index() {
        try {

            // Query to fetch activities with optional filtering
            $activities = AdminActivity::with(['user:id,name'])
                ->select('*')
                ->when(request()->query('type'), fn ($query) => $query->where('on', request()->query('type')))
                ->when(request()->query('action'), fn ($query) => $query->where('action', request()->query('action')))
                ->when(request()->query('user'), fn ($query) => $query->where('user_id', request()->query('user')))
                ->orderBy('created_at','DESC');

            // Getting list of available types
            $types = AdminActivity::distinct('on')->pluck('on');

            // Getting list of available actions
            $actions = AdminActivity::distinct('action')->pluck('action');

            // Paginate the results and append query parameters to the pagination links
            $activities = $activities->paginate(request()->input('per_page'))->appends(request()->query());

            // Retrieve users associated with activities
            $users = User::join('admin_activities', 'admin_activities.user_id', 'users.id')->select('users.id','users.name')->groupBy('user_id')->get();

            // Success response with activities, types, actions, and users
            return [
                'activities' => $activities,
                'types' => $types,
                'actions' => $actions,
                'users' => $users
            ];

        } catch (\Exception $e) {
            // Error response in case of an exception
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Get activities for the given user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userIndex(User $user)
    {
        try {

            // Return the user activities as a JSON response
            return response()->json($this->activityRepository->getUserActivities($user));

        } catch (\Exception $e) {
            // Return an error response in case of an exception
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }
}