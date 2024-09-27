<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Billing\{Transaction, PromoCode};
use App\Models\Admin\{BillingDetail, Tax};
use App\Models\User\UserBillingDetail;
use App\Models\User;
use App\Http\Helper;
use Exception, DB;
use App\Rules\AmountFormatRule;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions for a specific user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(User $user){
        try {
            // Retrieve paginated transactions for the specified user
            $transactions = $user->transactions()
            ->select([
                '*',
                DB::raw("DATE_FORMAT(paid_at, '%Y-%m-%d') as paid_at_human"),
                DB::raw("DATE_FORMAT(refunded_at, '%Y-%m-%d') as refunded_at_human")
            ])->orderByDesc('created_at')
            ->paginate(request()->input('per_page'));
         
            // ✅ Success response: Return paginated transactions data
            return response()->json([
                "transactions" => $transactions,
            ],200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                "message" => "Something went wrong!",
            ],500);
        }
    }

    /**
     * Display a listing of all transactions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function allIndex(){
        try {
            $status = request()->has('status') ? request()->get('status') : 1;

            // Retrieve paginated transactions with user specified details
            $transactions = Transaction::with(['user' => function ($query) {
                $query->select('id', 'name', 'email', 'avatar', 'created_at');
            }])
           ->select([
                '*',
                DB::raw("DATE_FORMAT(paid_at, '%Y-%m-%d') as paid_at_human"),
                DB::raw("DATE_FORMAT(refunded_at, '%Y-%m-%d') as refunded_at_human")
            ])
            ->where('status', $status)
            ->orderByDesc('created_at')
            ->paginate(request()->input('per_page'));

            // ✅ Success response: Return paginated transactions data
            return response()->json([
                'transactions' => $transactions,
            ],200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                "message" => "Something went wrong!",
            ],500);
        }
    }

    /**
     * Store a newly created transaction in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, User $user) {

        // Validate incoming request data
        $request->validate([
            'transaction_id' => 'nullable|string|max:255',
            'service' => 'required|max:255',
            'base_amount' => ['required', 'numeric', new AmountFormatRule],
            'discount_amount' => ['required', 'numeric', new AmountFormatRule],
            'tax_amount' => ['required', 'numeric', new AmountFormatRule],
            'final_amount' => ['required', 'numeric', new AmountFormatRule],
            'tax_details' => 'nullable',
            'promo_code_id' => 'nullable',
            'payment_gateway' => 'required|in:Razorpay,Paypal,Stripe',
            'status' => 'required|in:0,1,2,3',
            'refund_id' => 'nullable|required_if:status,3|max:255',
            'refund_reason' => 'nullable|required_if:status,3|max:255',
            'refunded_at_human' => 'nullable|required_if:status,3|date_format:Y-m-d',
            'payment_link' => 'nullable',
            'paid_at_human' => 'nullable|date_format:Y-m-d'
        ],[
            'service.required' => "The description field is required.",
            'refund_id.required_if' => 'The refund id field is required.',
            'refund_reason.required_if' => 'The refund reason field is required.',
            'refunded_at_human.required_if' => 'The refunded at field is required.',
        ]);

        try {
            // Fetch the company's billing details and user's billing details
            $companyBillingDetails = BillingDetail::first();
            $billingDetail = $user->billingDetail;
            
             // Construct address details using helper function
            $companyAddress = $this->getDetails($companyBillingDetails);
            $billingAddress = $this->getDetails($billingDetail);
            
            // Create a new transaction record for the user
            $transaction = $user->transactions()->create([
                'promo_code_id' => $request->promo_code_id,
                'transaction_id' => $request->transaction_id,
                'service' => $request->service,
                'base_amount' => $request->base_amount,
                'discount_amount' => $request->discount_amount,
                'tax_amount' => $request->tax_amount,
                'final_amount' => $request->final_amount,
                'payment_gateway' => $request->payment_gateway,
                'key' => Helper::generateUniqueToken(32, 'transactions', 'key'),
                'status' => $request->status,
                'refund_id' => $request->status == 3 ? $request->refund_id : null,
                'refund_reason' => $request->status == 3 ?$request->refund_reason : null,
                'refunded_at' => $request->status == 3 ?$request->refunded_at_human : null,
                'company_address' => $companyAddress,
                'company_tax_numbers' => $companyBillingDetails ? $companyBillingDetails->tax_numbers : null,
                'tax_details' => $request->tax_details,
                'address' => $billingAddress,
                'tax_numbers' => $billingDetail ? $billingDetail->tax_numbers : null,
                'payment_link' => $request->payment_link,
                'paid_at' => $request->paid_at_human,
            ]);

            if($request->promo_code_id && $request->status == 1 && $promoCode = PromoCode::where('id', $request->promo_code_id)->first()) {
                $promoCode->usage = $promoCode->usage + 1;
                if($promoCode->usable){
                    $promoCode->expires_at = $promoCode->usable <= $promoCode->usage?now():$promoCode->expires_at;
                }
                $promoCode->save();
            }

            // Store Activity
            Helper::adminActivity(auth()->user(), 'Transaction', 'Create', 'Transaction (#' . $transaction->id . ') has been created.');

            // Success response: Transaction created
            return response()->json([
                "message" => 'Transaction created successfully',
            ],200);
        } catch (\Exception $e) {
            // Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ],500);
        }
    }

    /**
     * Retrieve and display the specified transaction for a given user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user, Transaction $transaction){
        try {
            // Check if the transaction belongs to the specified user
            if (!$user->transactions()->find($transaction->id)) {
                 return response()->json([
                    "message" => "Record not found!",
                ],404);
            }

            // Retrieve the specific transaction details for the user
            $transaction = $user->transactions()->where('id', $transaction->id)
                ->select('id', 'promo_code_id', 'transaction_id', 'service', 'base_amount', 'discount_amount', 'tax_amount', 'final_amount', 'payment_gateway', 'status', 'refund_id', 'refund_reason', 'payment_link', 'tax_details',  \DB::raw('DATE_FORMAT(paid_at, "%Y-%m-%d") AS paid_at'), \DB::raw('DATE_FORMAT(refunded_at, "%Y-%m-%d") AS refunded_at'))
                ->first();

            // ✅ Success response: Return transactions data
            return response()->json([
                'transaction' => $transaction,
            ],200);
        } catch (\Exception $e) {
           // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                "message" => "Something went wrong!",
            ],500);
        }
    }

    /**
     * Update a transaction belonging to the user.
     *
     * @param Request $request The incoming request data.
     * @param Transaction $transaction The transaction to be updated.
     * @return \Illuminate\Http\JsonResponse Response indicating success or failure.
     */
    public function update(Request $request, User $user, Transaction $transaction) {
        // Validate incoming request
        $request->validate([
            'transaction_id' => 'required|string|max:255',
            'service' => 'required|max:255',
            'base_amount' => ['required', 'numeric', new AmountFormatRule],
            'discount_amount' => ['required', 'numeric', new AmountFormatRule],
            'tax_amount' => ['required', 'numeric', new AmountFormatRule],
            'final_amount' => ['required', 'numeric', new AmountFormatRule],
            'tax_details' => 'nullable',
            'promo_code_id' => 'nullable',
            'payment_gateway' => 'required|in:Razorpay,Paypal,Stripe',
            'status' => 'required|in:0,1,2,3',
            'refund_id' => 'nullable|required_if:status,3|max:255',
            'refund_reason' => 'nullable|required_if:status,3|max:255',
            'refunded_at_human' => 'nullable|required_if:status,3|date_format:Y-m-d',
            'payment_link' => 'nullable',
            'paid_at_human' => 'nullable|date_format:Y-m-d',
        ],[
            'service.required' => 'The description field is required.',
            'refund_id.required_if' => 'The refund id field is required.',
            'refund_reason.required_if' => 'The refund reason field is required.',
            'refunded_at_human.required_if' => 'The refunded at field is required.',
        ]);
     
        $transaction = $user->transactions()->findOrFail($transaction->id);
        
        try {
            // Promocode update
            if($transaction->status != 1 && $request->promo_code_id && $request->status == 1 && $promoCode = PromoCode::where('id', $request->promo_code_id)->first()) {
                $promoCode->usage = $promoCode->usage + 1;
                if($promoCode->usable) {
                    $promoCode->expires_at = $promoCode->usable <= $promoCode->usage?now():$promoCode->expires_at;
                }
                $promoCode->save();
            }

            // Determine refunded at date
            $refundedAt = $request->refunded_at_human && $request->refunded_at_human != "0000-00-00 00:00:00" ? $request->refunded_at_human : null;

            // Determine paid at date
            $paidAt = $request->paid_at_human && $request->paid_at_human != "0000-00-00 00:00:00" ? $request->paid_at_human : null;

            // Update transaction data
            $transaction->promo_code_id = $request->promo_code_id;
            $transaction->transaction_id = $request->transaction_id;
            $transaction->service = $request->service;
            $transaction->base_amount = $request->base_amount;
            $transaction->discount_amount = $request->discount_amount;
            $transaction->tax_amount = $request->tax_amount;
            $transaction->tax_details = $request->tax_details;
            $transaction->final_amount = $request->final_amount;
            $transaction->payment_gateway = $request->payment_gateway;
            $transaction->status = $request->status;
            $transaction->payment_link = $request->payment_link;
            $transaction->refund_id = $request->refund_id;
            $transaction->refunded_at = $refundedAt;
            $transaction->refund_reason = $request->refund_reason;
            $transaction->paid_at = $paidAt;
            $transaction->save();

            // Store Activity
            Helper::adminActivity(auth()->user(), 'Transaction', 'Update', 'Transaction (#' . $transaction->id . ') has been updated.');

            // ✅ Success response: Transaction updated successfully
            return response()->json([
                "message" => "Transaction updated successfully.",
            ],200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                "message" => "Something went wrong!",
            ],500);
        }
    }

    /**
     * Delete a transaction belonging to the user.
     *
     * @param User $user The user performing the action.
     * @param Transaction $transaction The transaction to be deleted.
     * @return \Illuminate\Http\JsonResponse Response indicating success or failure.
     */
    public function delete(User $user, Transaction $transaction){
        // Find the transaction belonging to the user or throw a 404 error if not found
        $transaction = $user->transactions()->findOrFail($transaction->id);

        try {
            // Store the ID of the transaction before deleting it
            $transactionId = $transaction->id;

            // Delete Transaction
            $transaction->delete();

            // Store Activity
            Helper::adminActivity(auth()->user(), 'Transaction', 'Delete', 'Transaction (#' . $transactionId . ') has been deleted.');

              // ✅ Success response: Transaction deleted successfully
            return response()->json([
                "message" => "Transaction deleted successfully.",
            ],200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ],500);
        }
    }

    /**
     * Helper function to construct billing details.
     *
     * @param mixed $details
     * @return array|null
     */
    protected function getDetails($details){
        return $details ? [
            'name' => $details->name ?? null,
            'email' => $details->email ?? null,
            'address' => $details->address .", ". $details->city . ", " . $details->region . ", " . $details->country . ", " . $details->postal_code,
        ] : null;
    }

    /**
     * Calculate the tax amount based on the provided base amount and user's location.
     *
     * @param User $user The user for whom the tax amount is calculated.
     * @return \Illuminate\Http\JsonResponse JSON response with tax details or an error message.
     */
    public function calculateTaxAmount(Request $request, User $user) {

        // Retrieve the billing details
        $companyBillingDetails = BillingDetail::first();

         // Determine the minimum credits required for a transaction
        $minimumCredits = 1;
        if($companyBillingDetails && $companyBillingDetails->minimum_recharge_amount > 0) {
            $minimumCredits = (double) $companyBillingDetails->minimum_recharge_amount;
        }

        // Validate incoming request data
        $request->validate([
            'amount' => ['required', 'numeric', 'min:' . $minimumCredits, new AmountFormatRule],
            'promo_code' => 'nullable|alpha_num',
        ]);

        try {

            $promoCode = null;
            $discountAmount = 0;
            $baseAmount = round($request->amount, 3);

            if(!empty($request->promo_code)) {
                $promoCode = PromoCode::where('code', $request->promo_code)->first();
                if(!$promoCode) {
                    return response()->json([
                        'message' => "Invalid promo code!",
                        'errors' => [
                            'promo_code' => ['Invalid promo code!'],
                        ]
                    ],422);
                }

                // Check promo code validity and expiration
                $message = Helper::checkPromoCodeValidate($user, $promoCode);
                if(is_bool($message) == false) {
                    return response()->json([
                        'message' => $message,
                        'errors' => [
                            'promo_code' => [$message],
                        ]
                    ],422);
                }

                if(isset($promoCode) && $promoCode->type == 'discount' && $promoCode->discount > 0){
                    // Calculate discount amount
                    $discountAmount = round($promoCode->discount * ($baseAmount) / 100, 3);
                }
    
                // Calculate base amount after applying discount
                $baseAmount = $baseAmount - $discountAmount;
            }

            $data = [];
            $data['base_amount'] = $baseAmount;
            $calculatedTaxDetail = Helper::calculateTaxAmount($baseAmount, $user->getCountryCodeValue(), $user->getRegionCodeValue());
            $data['tax_amount'] = $calculatedTaxDetail['tax_amount'];
            $data['discount_amount'] = $discountAmount;
            $data['final_amount'] = $baseAmount + $data['tax_amount'];
            $data['tax_detail'] = $calculatedTaxDetail['tax_applied'];

            return response()->json([
                'checkout' => $data,
                'promo_code' => $promoCode
            ]);
        }catch (\Exception $e) {
            report($e);
            return response()->json([
                "message" => $e->getMessage() ? $e->getMessage() : "Something went really wrong!",
            ], 500);
        }
    }
}