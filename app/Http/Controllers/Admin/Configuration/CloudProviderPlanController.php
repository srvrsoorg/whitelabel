<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Configuration\{CloudProvider, Plan};
use App\Http\Helper;

class CloudProviderPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @return \Illuminate\Http\Response
     */
    public function index(CloudProvider $cloudProvider)
    {
        try {
            // Retrieve distinct regions and their codes associated with the cloud provider's plans
            $regions = $cloudProvider->plans()->select('region', 'region_code')->distinct('region')->get();

            // Initialize an array to hold plans organized by region
            $planArray = array();

            // Loop through each distinct region
            foreach($regions as $region) {
                // Get all plans for the current region
                $plans = $cloudProvider->plans()->whereRegion($region->region)->get();

                // Add the region and its associated plans to the planArray
                array_push($planArray, ['region' => $region->region, 'region_code' => $region->region_code, 'plans' => $plans]);
            }

            // Success response
            return response()->json([
                'plans' => $planArray
            ], 200);
        } catch (\Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong while fetching plans!"
            ],500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, CloudProvider $cloudProvider)
    {
        // Validate the incoming request data
        $request->validate([
            "region" => "required|string",
            "region_code" => "nullable|string",
            "plans" => "required|array",
        ]);

        try {
            // Loop through each plan provided in the request
            foreach($request->plans as $plan) {
                // Ensure 'region' and 'slug' are set before proceeding
                if(isset($request->region) && isset($plan['slug'])) {
                    if(isset($plan['is_visible']) && $plan['is_visible']) {
                        // Update or create a new plan entry if the plan is visible
                        $serverPlan = $cloudProvider->plans()->updateOrCreate([
                            'region' => $request->region,
                            'size_slug' => $plan['slug']
                        ],[
                            'region_code' => $request->region_code,
                            'name' => isset($plan['plan_name'])?$plan['plan_name']:null,
                            'type' => $plan['name'],
                            'cores' => $plan['cpu_core'],
                            'ram' => $plan['ram_size_in_mb'],
                            'disk' => $plan['disk_size_in_gb'],
                            'bandwidth' => isset($plan['bandwidth'])?$plan['bandwidth']:20,
                            'server_price' => $plan['price'],
                            'price_per_month' => $plan['amount'],
                            'visible' => $plan['is_visible'],
                        ]);
                    } else if($serverPlan = $cloudProvider->plans()->whereRegion($request->region)->where('size_slug', $plan['slug'])->first()) {
                        // If the plan is not visible, update its visibility status to false
                        $serverPlan->visible = false; 
                        $serverPlan->save();
                    }
                }
            }

            // Store Admin Activity
            Helper::adminActivity(auth()->user(), 'Cloud Platform Plan', 'Update', 'Cloud platform plans have been updated in the ' . $cloudProvider->provider . ' account.');

            // Success response
            return response()->json([
                'message' => "Cloud platform plans updated successfully."
            ], 200);
        } catch(\Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong while creating plans!"
            ],500);
        }
    }
}
