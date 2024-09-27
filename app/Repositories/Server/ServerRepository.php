<?php

namespace App\Repositories\Server;

use App\Interfaces\Server\ServerRepositoryInterface;
use App\Models\Server\Server;
use App\Http\Utilities\Client;
use Carbon\Carbon;

class ServerRepository implements ServerRepositoryInterface
{
	/**
     * Retrieve the server with its associated subscription and usage summaries.
     *
     * @param  mixed  $server  The server object or identifier.
     * @return array
     */
	public function getServer($server)
	{
        // Retrieve the server by its ID and load related models
		$server = Server::where('id', $server->id)->select('*')
			->with([
				'user:id,name,email', 
				'cloudProvider:id,provider',
				'subscription:id,server_id,plan_id,monthly_price,created_at',
			])
			->first();

        // Get the subscription associated with the server, if it exists
		$subscription = $server->subscription;
        $usageSummaries = $subscription ? $subscription->usageSummaries : null;

	    // Initialize variables for total hours and total charges
	    $totalHours = 0;
	    $totalCharges = 0;
	    $startDate = null;

	    // Check if there are usage summaries available
        if ($usageSummaries && $usageSummaries->isNotEmpty()) {
	        // Get the earliest started_at date
	        $startDate = $usageSummaries->min('created_at');
	        $last_deduct = $usageSummaries->max('last_deduct_at');

            if ($startDate && $last_deduct) {

		        $last_deduct_at = Carbon::parse($last_deduct)->timezone(optional(auth()->user())->timezone ?? config('app.timezone'))->format('Y-m-d H:i:s');

		        // Add the difference from the earliest started_at to now
		        $totalHours += Carbon::parse($startDate)->diffInHours($last_deduct_at);

		        // Calculate total charges
		        $totalCharges = $usageSummaries->sum('deduct_amount');
		    }
	    }

        if ($subscription && $startDate) {
	    	// Format created_at date for the subscription
		    $subscription->formatted_created_at = Carbon::parse($startDate)->format('M d, Y H:i:s');
		    $subscription->total_hours = $totalHours;
		    $subscription->total_charges = $totalCharges;
		}

	    unset($subscription->usageSummaries);

		return [
			'server' => $server,
		];
	}
}