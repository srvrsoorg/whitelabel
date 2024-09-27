<?php

namespace App\Repositories\UsageSummary;

use App\Interfaces\UsageSummary\UsageSummaryRepositoryInterface;
use App\Models\User;
use Carbon\Carbon;

class UsageSummaryRepository implements UsageSummaryRepositoryInterface
{
    /**
     * Retrieve usage summaries for a specific user based on request filters.
     *
     * @param  \Illuminate\Http\Request  $request  The request containing filters and pagination info.
     * @param  \App\Models\User  $user  The user for whom to retrieve usage summaries.
     * @return array
     */
	public function getUsageSummary($request, $user)
    {
		// Retrive User
		$user = User::find($user->id);

		// Start building the query
        $query = $user->usageSummaries()
            ->with(['subscription:id,monthly_price', 'server:id,ip,name']);

        // Calculate the total deducted amount for the requested month
        $monthlyDeductedAmount = $user->usageSummaries()->whereMonth('started_at', now()->month)
            ->whereYear('started_at', now()->year)
            ->sum('deduct_amount');

        // Apply year filter if present
        $query->when($request->query('year'), function($query) use ($request) {
            $query->whereYear('started_at', $request->query('year'));
        });

        // Apply month filter if present
        $query->when($request->query('month'), function($query) use ($request) {
            $query->whereMonth('started_at', $request->query('month'));
        });

        $filterBaseMonthUsage = $query->sum('deduct_amount');

        // Apply search filter for IP and name if present
        $query->when($request->query('search'), function($query) use ($request) {
            $searchTerm = $request->query('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('server_ip', 'like', '%' . $searchTerm . '%')
                  ->orWhere('server_name', 'like', '%' . $searchTerm . '%');
            });
        });


        // Calculate the total deducted amount from creation date to now for all servers
        $totalDeductedAmount = $user->usageSummaries()->sum('deduct_amount');

        // Execute the query and get the results
        $usageSummaries = $query->orderBy('created_at', 'desc')->paginate(request()->input('per_page'));

        // Adjust the last_deduct_at timezones
        $usageSummaries->getCollection()->transform(function ($usageSummary) use ($user) {
            $createdAt = $usageSummary->created_at;
            $lastDeductAt = Carbon::parse($usageSummary->last_deduct_at)
                ->timezone(optional($user)->timezone ?? config('app.timezone'))
                ->format('Y-m-d H:i:s');

            $usageSummary->hours = Carbon::parse($createdAt)->diffInHours($lastDeductAt);
            $usageSummary->last_deduct_at = $lastDeductAt;
            return $usageSummary;
        });

        return [
            "monthly_deducted_amount" => $monthlyDeductedAmount,
            "filter_based_monthly_usage" => $filterBaseMonthUsage,
            "total_deducted_amount" => $totalDeductedAmount,
            "usageSummaries" => $usageSummaries,
        ];
	}
}