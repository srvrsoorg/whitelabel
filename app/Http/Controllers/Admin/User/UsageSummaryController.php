<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Interfaces\UsageSummary\UsageSummaryRepositoryInterface;

class UsageSummaryController extends Controller
{
    /**
     * Create a new controller instance and inject dependencies.
     *
     * @param UsageSummaryRepositoryInterface $usageSummaryRepository
     * @return void
     */
    public function __construct(protected UsageSummaryRepositoryInterface $usageSummaryRepository){
        $this->usageSummaryRepository = $usageSummaryRepository;
    }

    /**
     * Display a listing of the usage summaries for a specific user.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, User $user){
        try {
            // Fetch usage summaries for the specified user using the repository
            $usageSummaries = $this->usageSummaryRepository->getUsageSummary($request, $user);

            // Return the usage summaries in JSON format
            return response()->json($usageSummaries);
        } catch (\Exception $e) {
            // Log the exception and return an error message
            report($e);
            return response()->json([
                "message" => "Something went really wrong!"
            ],500);
        }
    }   
}
