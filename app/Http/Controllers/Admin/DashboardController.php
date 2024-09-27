<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Billing\Transaction;
use App\Models\Server\{Server, Subscription};
use App\Http\Helper;
use Carbon\Carbon;
use Log;
use DB;

class DashboardController extends Controller
{
    
    /**
     * Resolve and validate the model.
     *
     * @param string $model
     * @return array
     */
    private function resolveModel($model)
    {
        $validModels = [
            'User' => 'App\Models\User',
            'Transaction' => 'App\Models\Billing\Transaction',
            'Server' => 'App\Models\Server\Server',
            'Subscription' => 'App\Models\Server\Subscription',
            'Ticket' => 'App\Models\Ticket'
        ];

        $model = ucfirst($model);
        if (!array_key_exists($model, $validModels)) {
            return [null, 'Invalid Parameter!'];
        }

        return [app($validModels[$model]), null];
    }

    /**
     * Get a summary of data for a specified model.
     *
     * @param string $model The model name for the summary.
     * @return \Illuminate\Http\JsonResponse Summary data in JSON format.
     */
    public function getSumary($model)
    {
        try {
            
            list($modelInstance, $error) = $this->resolveModel($model);
            if ($error) {
                return response()->json(['message' => $error], 500);
            }

            // Convert the model name to lowercase for prefixing
            $modelPrefix = strtolower($model);

            // Define date ranges
            $today = Carbon::today();
            $yesterday = Carbon::yesterday();
            $last7Days = Carbon::now()->subDays(7);
            $last30Days = Carbon::now()->subDays(30);
            $last6Months = Carbon::now()->subMonths(6);
            
            // Previous periods for comparison
            $previous7Days = Carbon::now()->subDays(14)->startOfDay();
            $previous30Days = Carbon::now()->subDays(60)->startOfDay();
            $previous6Months = Carbon::now()->subMonths(12)->startOfDay();

            // Define function to apply conditional logic
            $applyConditions = function($query, $modelPrefix) {
                if ($modelPrefix == 'transaction') {
                    return $query->where('status', 1)->sum('final_amount');
                } else if($modelPrefix == 'subscription') {
                    return $query->sum('monthly_price');
                } else {
                    return $query->count();
                }
            };

            // Perform the queries
            $todayCount = $applyConditions(
                $modelInstance->whereDate('created_at', $today),
                $modelPrefix
            );

            $yesterdayCount = $applyConditions(
                $modelInstance->whereDate('created_at', $yesterday),
                $modelPrefix
            );

            $last7DaysCount = $applyConditions(
                $modelInstance->whereDate('created_at', '>=', $last7Days),
                $modelPrefix
            );

            $previous7DaysCount = $applyConditions(
                $modelInstance->whereBetween('created_at', [$previous7Days, $last7Days]),
                $modelPrefix
            );

            $last30DaysCount = $applyConditions(
                $modelInstance->whereDate('created_at', '>=', $last30Days),
                $modelPrefix
            );

            $previous30DaysCount = $applyConditions(
                $modelInstance->whereBetween('created_at', [$previous30Days, $last30Days]),
                $modelPrefix
            );

            $last6MonthsCount = $applyConditions(
                $modelInstance->whereDate('created_at', '>=', $last6Months),
                $modelPrefix
            );

            $previous6MonthsCount = $applyConditions(
                $modelInstance->whereBetween('created_at', [$previous6Months, $last6Months]),
                $modelPrefix
            );

            $totalCount = $applyConditions($modelInstance, $modelPrefix);

            // Calculate percentages
            $todayPercentage = $this->calculatePercentageChange($yesterdayCount, $todayCount);
            $last7DaysPercentage = $this->calculatePercentageChange($previous7DaysCount, $last7DaysCount);
            $last30DaysPercentage = $this->calculatePercentageChange($previous30DaysCount, $last30DaysCount);
            $last6MonthsPercentage = $this->calculatePercentageChange($previous6MonthsCount, $last6MonthsCount);

            // Return the response with counts and percentage changes
            return response()->json([
                "{$modelPrefix}_today" => $todayCount,
                "{$modelPrefix}_yesterday" => $yesterdayCount,
                "{$modelPrefix}_today_percentage" => $todayPercentage,
                "{$modelPrefix}_last7Days" => $last7DaysCount,
                "{$modelPrefix}_previous7Days" => $previous7DaysCount,
                "{$modelPrefix}_last7Days_percentage" => $last7DaysPercentage,
                "{$modelPrefix}_last30Days" => $last30DaysCount,
                "{$modelPrefix}_previous30Days" => $previous30DaysCount,
                "{$modelPrefix}_last30Days_percentage" => $last30DaysPercentage,
                "{$modelPrefix}_last6Months" => $last6MonthsCount,
                "{$modelPrefix}_previous6Months" => $previous6MonthsCount,
                "{$modelPrefix}_last6Months_percentage" => $last6MonthsPercentage,
                "{$modelPrefix}_total" => $totalCount
            ]);

        } catch (\Exception $e) {
            // Handle any exceptions that occur and return a generic error response
            return response()->json([
                'message' => 'An error occurred while fetching the summary!',
                'error' => $e->getMessage()  // Optionally include the exception message for debugging
            ], 500);
        }
    }

    /**
     * Calculate percentage change
     *
     * @param int $previousCount
     * @param int $currentCount
     * @return float|null
     */
    private function calculatePercentageChange($previousCount, $currentCount)
    {
        if ($previousCount == 0) {
            return 100; // To avoid division by zero
        }

        $change = (($currentCount - $previousCount) / $previousCount) * 100;
        return round($change, 2); // Return the change rounded to two decimal places
    }

    /**
     * Get chart data for a specific model based on the filter.
     *
     * @param string $model
     * @param string $filter
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChartData($model, $filter)
    {
        try {
            // Validate the model parameter
            $model = ucfirst($model);
            list($modelInstance, $error) = $this->resolveModel($model);
            if ($error) {
                return response()->json(['message' => $error], 500);
            }

            // Validate the filter parameter
            if (!in_array($filter, ['sevenDay', 'last30Days', 'sixMonth', 'thisYear'])) {
                return response()->json([
                    "message" => "Invalid Filter Parameter!"
                ], 500);
            }

            // Define the date range based on the filter
            $dateQuery = function($query) use ($filter) {
                if ($filter == 'sevenDay') {
                    $query->whereDate('created_at', '>=', Carbon::now()->subDays(7));
                } else if ($filter == 'last30Days') {
                    $query->whereDate('created_at', '>=', Carbon::now()->subDays(30));
                } else if ($filter == 'sixMonth') {
                    $query->whereDate('created_at', '>=', Carbon::now()->subMonths(6));
                } else if ($filter == 'thisYear') {
                    $query->whereDate('created_at', '>=', Carbon::now()->startOfYear());
                }
            };

            // Perform the query
            $data = $modelInstance->where(function($query) use ($dateQuery) {
                    $dateQuery($query);
                })
                ->select(DB::raw("DATE(created_at) as date"))
                ->when($model == 'Transaction', function($query) {
                    $query->where('status', 1)->addSelect(DB::raw("SUM(final_amount) as total_amount"));
                })->when($model == 'Subscription', function($query) {
                    $query->addSelect(DB::raw("SUM(monthly_price) as total_amount"));
                })->when(!in_array($model,['Transaction', 'Subscription']), function($query) {
                    $query->addSelect(DB::raw("COUNT(*) as count"));
                })
                ->groupBy('date')
                ->orderBy('date', 'ASC')
                ->get();

            // Convert the model name to lowercase for prefixing
            $modelPrefix = strtolower($model);

            // Return the response with the chart data
            return response()->json([
                "{$model}_chart_data" => $data
            ]);

        } catch (\Exception $e) {
            // Handle any exceptions that occur and return a generic error response
            return response()->json([
                'message' => 'An error occurred while fetching the chart data!',
                'error' => $e->getMessage()  // Optionally include the exception message for debugging
            ], 500);
        }
    }

    /**
     * Get the count of users grouped by country.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function usersByCountry()
    {
        try {
            // Retrieve the count of users grouped by country
            $usersByCountry = User::select('country_name', DB::raw('count(*) as total'))
                ->groupBy('country_name')
                ->orderBy('total', 'DESC')
                ->get();

            // Calculate the total number of users
            $totalUsers = $usersByCountry->sum('total');

            // Calculate percentages for each country
            $usersByCountryWithPercentage = $usersByCountry->map(function ($item) use ($totalUsers) {
                $percentage = $totalUsers > 0 ? ($item->total / $totalUsers) * 100 : 0;
                $item->percentage = round($percentage, 2); // Round to 2 decimal places
                return $item;
            });

            // Return the results as a JSON response
            return response()->json([
                'totalUsers' => $totalUsers,
                'usersByCountry' => $usersByCountry,
            ]);

        } catch (\Exception $e) {
            // Log the error and return a JSON error response
            Log::error('Error fetching users by country: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching users by country!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the count of servers based on connection status.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function serverConnectionCounts()
    {
        try {
            // Retrieve the count of connected and disconnected servers
            $serverStatusCounts = Server::select(
                DB::raw("CASE WHEN agent_status = 1 THEN 'connected' ELSE 'disconnected' END as status"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

            // Return the results as a JSON response
            return response()->json([
                'serverStatusCounts' => $serverStatusCounts,
            ]);

        } catch (\Exception $e) {
            // Log the error and return a JSON error response
            Log::error('Error fetching server connection counts: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching server connection counts!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
