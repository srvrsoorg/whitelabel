<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
     * Get activities for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userIndex()
    {
        try {
            // Retrieve the authenticated user
            $user = auth()->user();

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
