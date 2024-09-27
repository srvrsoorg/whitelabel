<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Tax;
use App\Http\Helper;

class TaxController extends Controller
{
    /**
     * Display a listing of the tax records, grouped by type.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Fetch all tax records
            $taxes = Tax::all()->groupBy('type');
        
            // Initialize the response array
            $response = [
                'tax' => [
                    'default' => [],
                    'country' => [],
                ]
            ];
        
            // Create associative arrays for country lookup
            $countryMap = [];
        
            // Process the tax records
            foreach ($taxes as $type => $records) {
                foreach ($records as $tax) {
                    if ($type == 'default') {
                        $response['tax']['default'][] = [
                            'tax' => $tax->tax,
                            'label' => $tax->label,
                        ];
                    } else {
                        // Check if the country already exists in the response array
                        if (!isset($countryMap[$tax->country_code])) {
                            // Add a new country entry if it doesn't exist
                            $countryIndex = count($response['tax']['country']);
                            $countryMap[$tax->country_code] = $countryIndex;
                            $response['tax']['country'][$countryIndex] = [
                                'country' => $tax->country,
                                'country_code' => $tax->country_code,
                                'regions' => [],
                            ];
                        }
        
                        // Add the region data to the corresponding country
                        $countryIndex = $countryMap[$tax->country_code];
                        $response['tax']['country'][$countryIndex]['regions'][] = [
                            'country' => $tax->country,
                            'country_code'=> $tax->country_code,
                            'region' => $tax->region,
                            'region_code' => $tax->region_code,
                            'tax' => $tax->tax,
                            'label' => $tax->label,
                        ];
                    }
                }
            }
        
            // Return the formatted response as JSON
            return response()->json($response);
        } catch (\Exception $e) {
            // Report the error for debugging
            report($e);

            // Return an error response
            return response()->json(['message' => "Something went really wrong!"], 500);
        }
    }

    /**
     * Store or update tax records based on the provided request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'tax' => 'required|array',
            'tax.default' => 'required|array',
            'tax.default.*.tax' => 'required|numeric|min:0|max:100',
            'tax.default.*.label' => 'required|string|max:255',
            
            'tax.country' => 'nullable|array',
            'tax.country.*.country' => 'required_with:tax.country|string',
            'tax.country.*.country_code' => 'required_with:tax.country|string',
            'tax.country.*.regions' => 'required_with:tax.country|array|min:1',
            
            'tax.country.*.regions.*.region' => 'required|string',
            'tax.country.*.regions.*.region_code' => 'required|string',
            'tax.country.*.regions.*.tax' => 'required|numeric|min:0|max:100',
            'tax.country.*.regions.*.label' => 'required|string|max:255',
        ],[
            'tax.country.*.regions.required_with' => 'Please provide at least one region for the selected country.',
            'tax.country.*.regions.array' => 'The regions must be provided in a valid format.',
            'tax.country.*.regions.min' => 'Please add at least one region for the selected country.',
            'tax.default.*.tax.required' => 'The tax rate is required for the default tax.',
            'tax.default.*.tax.min' => 'The tax rate for the default tax must be at least 0%.',
            'tax.default.*.tax.max' => 'The tax rate for the default tax cannot exceed 100%.',
            'tax.default.*.label.required' => 'The tax label is required for the default tax.',
            'tax.country.*.country.required_with' => 'The country name is required when defining country-specific taxes.',
            'tax.country.*.country_code.required_with' => 'The country code is required.',
            'tax.country.*.regions.*.region.required' => 'The region name is required for each specified region.',
            'tax.country.*.regions.*.region_code.required' => 'The region code is required for each specified region.',
            'tax.country.*.regions.*.tax.required' => 'The tax rate is required for each specified region.',
            'tax.country.*.regions.*.tax.numeric' => 'The tax rate must be a valid number between 0% and 100% for each specified region.',
            'tax.country.*.regions.*.tax.min' => 'The tax rate for each region must be at least 0%.',
            'tax.country.*.regions.*.tax.max' => 'The tax rate for each region cannot exceed 100%.',
            'tax.country.*.regions.*.label.required' => 'The tax label is required for each specified region.',
            'tax.country.*.regions.*.label.max' => 'The tax label for each region cannot exceed 255 characters.',
        ]);

        try {
            // Start a database transaction
            \DB::beginTransaction();

            // Fetch all existing tax records
            $taxArray = []; // Array to store new or updated records
            $newTaxIds = []; // Array to track the IDs of updated/created records

            foreach ($request->tax as $key => $value) {
                if ($key == "default") {
                    // Handle default tax case (no country or region)
                    $taxArray[] = [
                        'type' => $key,
                        'country' => null,
                        'country_code' => null,
                        'region' => null,
                        'region_code' => null,
                        'label' => $value[0]['label'],
                        'tax' => $value[0]['tax'],
                    ];
                } elseif ($key == "country") {
                    // Handle country-specific tax data and regions within the country
                    foreach ($value as $country) {
                        foreach ($country['regions'] as $region) {
                            $taxArray[] = [
                                'type' => $key, // 'country' type
                                'country' => $country['country'],
                                'country_code' => $country['country_code'],
                                'region' => $region['region'],
                                'region_code' => $region['region_code'],
                                'label' => $region['label'],
                                'tax' => $region['tax'],
                            ];
                        }
                    }
                }
            }

            // Process the tax data
            foreach ($taxArray as $taxData) {
                // We use the combination of 'type', 'country_code', 'region_code', and 'label' as unique
                $existingTax = Tax::where([
                    'type' => $taxData['type'],
                    'country_code' => $taxData['country_code'],
                    'region_code' => $taxData['region_code'],
                    'label' => $taxData['label'],
                ])->first();

                if ($existingTax) {
                    // Update existing record
                    $existingTax->update($taxData);
                    $newTaxIds[] = $existingTax->id;
                } else {
                    // Create new record
                    $newTax = Tax::create($taxData);
                    $newTaxIds[] = $newTax->id;
                }
            }

            // Delete any records that are not part of the newTaxIds array
            Tax::whereNotIn('id', $newTaxIds)->delete();

            // Commit the transaction if no issues occurred
            \DB::commit();

            // Log the admin activity for tax synchronization
            Helper::adminActivity(auth()->user(), 'Tax', 'Sync', 'Tax records have been synchronized.');

            // Return success response
            return response()->json([
                'message' => "Tax updated successfully."
            ], 200);

        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            \DB::rollBack();

            // Report the error for debugging
            report($e);

            // Return an error response
            return response()->json([
                'message' => "Something went wrong while synchronizing tax records!"
            ], 500);
        }
    }
}
