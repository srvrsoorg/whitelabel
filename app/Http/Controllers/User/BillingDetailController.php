<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helper;

class BillingDetailController extends Controller
{
    /**
     * Retrieve user billing details.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Retrieved authenticated user
            $user = auth()->user();

            // Fetch the billing details for the user
            $billingDetail = $user->billingDetail;

            // ✅ Success response with billing detail
            return response()->json([
                "billingDetail" => $billingDetail,
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
     * Store or update user billing details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        // Validation rules
        $validationArray = [
            'name' => ['required', 'string', 'regex:/^[\p{L}\s]+$/u', 'min:3', 'max:255'],
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|regex:/^[A-Z0-9\s\-]+$/i|min:3|max:15',
            'country' => 'required|string',
            'country_code' => 'required|string',
            'region' => 'required|string',
            'region_code' => 'required|string',
            'tax_numbers' => [
                "nullable",
                "array",
                function ($attribute, $value, $fail) {
                    foreach ($value as $taxVar) {
                        if (!isset($taxVar['name']) || !isset($taxVar['value'])) {
                            $fail("Each entry requires both a variable name and value.");
                            return;
                        }
                    }
                },
            ],
        ];

        // Validate the incoming request
        $request->validate($validationArray);

        try {
            // Retrieved authenticated user
            $user = auth()->user();

            $billingDetailData = [
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'tax_numbers' => $request->tax_numbers,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'country_code' => $request->country_code,
                'region' => $request->region,
                'region_code' => $request->region_code,
            ];

            // Update or create the billing details for the user
            $user->billingDetail()->updateOrCreate(['user_id' => $user->id], $billingDetailData);

            // Store Activity
            Helper::createActivity($user, 'Billing', 'Update', 'Billing details have been updated.');

            // Success response: Return Billing Detail and a success message
            return response()->json([
                "message" => "Billing Detail has been updated successfully.",
            ], 200);
        } catch (\Exception $e) {
            // Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ], 500);
        }
    }
}