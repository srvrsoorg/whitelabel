<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Billing\PromoCode;
use App\Models\User;
use App\Http\Helper;
use Exception;

class PromoCodeController extends Controller
{
    /**
     * Get a paginated list of promo codes filtered and sorted by search query and sorting criteria.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        try {
            // Retrieve search query and sorting criteria from request
            $search = request()->input('search');
            $sorting = request()->input('sorting');

            // Fetch promo codes with optional search and sorting
            $promoCodes = PromoCode::when($search, fn ($query) => $query->where('code', 'LIKE', '%' . $search . '%'))
                ->when($sorting, fn ($query) => $query->orderBy(explode('-',$sorting)[0],explode('-',$sorting)[1]))
                ->select('*')
                ->selectRaw("DATE_FORMAT(expires_at, '%Y-%m-%d') as expires_date")
                ->orderByDesc('created_at')
                ->paginate(request()->input('per_page'));

            $promoCodes->map(function ($promoCode) {
                $promoCode->usage_status = $this->getUsage($promoCode);
                unset($promoCode->expires_at);
            });

            // Return paginated list of promo codes
            return response()->json([
                "promoCodes" => $promoCodes,
            ],200);  
        } catch (\Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                "message" => "Something went wrong!",
            ],500);
        }
    }

    /**
     * Get a list of available promo codes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function availablePromoCode(){
        try {
            
            // Fetch promo codes with optional search and sorting
            $promoCodes = PromoCode::select('id', 'code', 'discount')
                ->orderByDesc('created_at')
                ->get();

            // Return paginated list of promo codes
            return response()->json([
                "promoCodes" => $promoCodes,
            ],200);  
        } catch (\Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                "message" => "Something went wrong!",
            ],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Coupon  $promoCode
     * @return \Illuminate\Http\Response
     */
    public function show($promo_code, User $user)
    {
        try {

            // Find promo code by requested code
            if(!$promoCode = PromoCode::where('code', $promo_code)->first()) {
                // Error response
                return response()->json([
                    'message' => "Invalid promo code!"
                ],500);
            }

            // Check promo code validity and expiration
            $message = Helper::checkPromoCodeValidate($user, $promoCode);
            if(is_bool($message) == false) {
                // Error response
                return response()->json([
                    'message' => $message
                ],500);
            }

            // Check if promo code is active
            if($promoCode->expires_at) {
                $promoCode->active = $promoCode->expires_at->diffInHours(now(),false)<0?true:false;
            } else {
                $promoCode->active = true;
            }

            // Success response with promo code details
            return response()->json([
                'promo_code' => $promoCode
            ],200);
        } catch (\Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ],500);
        }
    }

    /**
     * Store a newly created promo code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){
        // Validate incoming request
        $request->validate([
            'code' => 'required|alpha_num|unique:promo_codes|max:255',
            'description' => 'required|string|max:255',
            'usable' => 'nullable|required_if:expires_date,null|integer|min:1',
            'discount' => 'required|numeric|min:1|max:99',
            'customer_type' => 'required|string|in:new_customer,all_customer',
            'expires_date' => [
                'nullable',
                'date_format:Y-m-d',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value !== null && $request->input('usable') !== null) {
                        $fail('The expires at field should only required if usable field is empty.');
                    }
                }
            ],
        ]);

        try {
            // Create the promo code
            $promoCode = PromoCode::create([
                'code' => $request->code,
                'description' => $request->description,
                'usable' => $request->usable ? $request->usable : 0,
                'discount' => $request->discount,
                'customer_type' => $request->customer_type,
                'expires_at' => $request->expires_date,
            ]);

            // Store Activity
            Helper::adminActivity(auth()->user(), 'Promo Code', 'Create', ucfirst($request->code) . ' promo code has been created.');

            // ✅ Success response: Promo Code Created Successfully.
            return response()->json([
                "message" => "Promo code created successfully.",
            ],200);
        } catch (\Exception $e) {
             // Log and handle exceptions
            report($e);
            return response()->json([
                "message" => "Something went wrong!",
            ],500);
        }
    }

    /**
     * Update an existing promo code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Billing\PromoCode  $promoCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, PromoCode $promoCode){
        // validating incoming request 
        $request->validate([
            'code' => 'required|alpha_num|max:255|unique:promo_codes,code,' . $promoCode->id,
            'description' => 'required|string|max:255',
            'usable' => 'nullable|required_if:expires_date,null|integer|min:1',
            'discount' => 'required|numeric|min:1|max:99',
            'customer_type' => 'required|string|max:255|in:new_customer,all_customer',
            'expires_date' => [
                'nullable',
                'date_format:Y-m-d',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value !== null && $request->input('usable') !== null) {
                        $fail('The expires at field should only required if usable field is empty.');
                    }
                }
            ],       
        ]);

        try {
            // Update Promo code
            $promoCode->code = $request->code;
            $promoCode->discount = $request->discount;
            $promoCode->description = $request->description;
            $promoCode->customer_type = $request->customer_type;

           // Set expiration date and usability
            if($request->expires_date && !$request->usable) {
                $promoCode->expires_at = $request->expires_date;
                $promoCode->usable = 0;
            } elseif ($request->usable && !$request->expires_date) {
                $promoCode->usable = $request->usable;
                $promoCode->expires_at = null;
            } else {
                $promoCode->expires_at = null;
                $promoCode->usable = 0;
            }

            // Store PromoCode data
            $promoCode->save();

            // Store Activity
            Helper::adminActivity(auth()->user(), 'Promo Code', 'Update', ucfirst($promoCode->code) . ' promo code has been updated.');

            // ✅ Success response: Promo Code Updated.
            return response()->json([
                "message" => "Promo code updated successfully.",
            ],200);
        } catch (\Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ],500);
        }
    }

    /**
     * Delete a promo code.
     *
     * @param  \App\Models\Billing\PromoCode  $promoCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(PromoCode $promoCode){
        try {
            // Store promo code name before deleting it
            $promoCodeName = $promoCode->code;

            // delete specified promo code
            $promoCode->delete();

            // Store Activity
            Helper::adminActivity(auth()->user(), 'Promo Code', 'Delete', ucfirst($promoCodeName) . ' promo code has been deleted.');

            // Success response
            return response()->json([
                "message" => "Promo code has been deleted.",
            ],200);
        } catch (\Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ],500);
        }
    }

    /**
     * Determine the usage status of a promo code.
     *
     * @param App\Models\Billing\PromoCode $promoCode
     * @return string|null
     */
    public function getUsage($promoCode){
        // Check if the promo code has expired
        if ($promoCode->expires_at && $promoCode->expires_at < now()) {
            return "Not Usable";

        // Check if the promo code's usage limit has been reached 
        } elseif (($promoCode->usable !== 0 && $promoCode->usable == $promoCode->usage)) {
            return "Not Applicable";

        // Check if the promo code can still be used and has remaining uses
        } elseif ($promoCode->usable !== 0 && $promoCode->usage < $promoCode->usable) {
            return "Limited(". $promoCode->usable - $promoCode->usage . " uses)";

        // Check if the promo code is unlimited and has not expired
        } elseif ($promoCode->usable == 0 && $promoCode->expires_at >= now()) {
            return "Unlimited";
        } else {

        // If none of the above conditions are met, return null
            return null;
        }
    }
}