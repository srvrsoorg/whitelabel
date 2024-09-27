<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User\UsageSummary;
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
     * Display a listing of the usage summaries with optional filters.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Fetch the authenticated user
            $user = auth()->user();

            // Use the repository to fetch usage summaries based on the request and user
            $usageSummaries = $this->usageSummaryRepository->getUsageSummary($request, $user);

            // Return the usage summaries in JSON format
            return response()->json($usageSummaries);
        } catch(\Exception $exception) {
            // Log the exception for debugging
            report($exception);
            
            // Return a JSON response with an error message
            return response()->json([
                "message" => "Error occurred while fetching usage summaries! Please try again later!"
            ],500);
        }
    }
}
