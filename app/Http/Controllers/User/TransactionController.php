<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Billing\{Transaction,PromoCode};
use App\Models\Admin\BillingDetail;
use App\Models\User\UserBillingDetail;
use App\Models\Admin\Configuration\Payment as PaymentConfig;
use App\Http\Helper;
use Razorpay\Api\Api;
use Laravel\Cashier\Cashier;
use App\Enums\PaymentGateway as PaymentGatewayEnum;
use App\Helpers\Currency;
use Mail;
use Exception;
use App\Rules\AmountFormatRule;
use App\Http\Utilities\Client;

// Paypal Library classes
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction as PaypalTransaction;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Mail\Transaction\TransactionSuccessful;
use App\Models\Admin\Tax;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        try {
            // Fetch transactions for the authenticated user and paginate the results
            $transactions = auth()->user()->transactions()
                ->select('id', 'payment_gateway', 'service', 'key', 'transaction_id', 'base_amount', 'final_amount', 'discount_amount', 'status', 'created_at')
                ->orderBy('created_at','DESC')
                ->paginate(request()->input('per_page'));

            // Return success response with the transactions
            return response()->json([
                'transactions' => $transactions
            ], 200);
        } catch (\Exception $e) {
            // Return error response if an exception occurs
            report($e);
            return response()->json([
                'message' => "Something went wrong while fetching transactions!"
            ], 500);
        }
    }

    /**
     * Display the details of a transaction identified by its key.
     *
     * @param string $key The unique key of the transaction.
     * @return \Illuminate\Http\JsonResponse Response containing the transaction details.
     */
    public function show($key) {
        try {
            $user = auth()->user();
            
            $transaction = $user->transactions()->where('key', $key)
                ->select('id', 'user_id', 'transaction_id', 'base_amount', 'payment_gateway', 'discount_amount', 'tax_amount', 'final_amount', 'status', 'service', 'tax_numbers', 'address', 'company_address', 'company_tax_numbers', 'tax_details', 'paid_at', 'created_at')
                ->first();

            if(!$transaction){
                return response()->json([
                    "message" => "Transaction not found!",
                ],404);
            }

            // Return success response with the transactions
            return response()->json([
                'transaction' => $transaction
            ],200);
        } catch (\Exception $e) {
            // Return error response if an exception occurs
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ],500);
        }
    }

    /**
     * Store a new transaction for purchasing credits.
     *
     * @param Request $request The incoming request data.
     * @return \Illuminate\Http\JsonResponse Response indicating success or failure.
     */
    public function store(Request $request){
        // Retrieve enabled payment providers
        $provider = PaymentConfig::where('enabled', true)->pluck('provider')->toArray();
        
        // Retrieve the billing details
        $companyBillingDetails = BillingDetail::first();

         // Determine the minimum credits required for a transaction
        $minimumCredits = 1;
        if($companyBillingDetails && $companyBillingDetails->minimum_recharge_amount > 0) {
            $minimumCredits = (double) $companyBillingDetails->minimum_recharge_amount;
        }

        // Validate incoming request data
        $request->validate([
            'credits' => ['required', 'numeric', 'min:' . $minimumCredits, new AmountFormatRule],
            'payment_gateway' => ['required','in:' . implode(",", $provider)],
            'promo_code' => 'nullable|alpha_num',
        ]);

        $user = auth()->user();

        // Check for a promo code and validate it if provided
        if($request->post('promo_code')){
            if($promoCode = PromoCode::where('code',$request->post('promo_code'))->first()) {
                $message = Helper::checkPromoCodeValidate($user, $promoCode);
                if(is_bool($message) == false) {
                    // Error response
                    return response()->json([
                        'message' => $message
                    ],500);
                }
            } else {
                // Error response
                return response()->json([
                    'message' => "Invalid promo code!"
                ],500);
            }
        }

        try {
            $discountAmount = 0;

            // Calculate discount amount if a valid promo code is applied
            if(isset($promoCode) && $promoCode->type == 'discount' && $promoCode->discount > 0){
                // Record the used promo code
                $user->metas()->create([
                    'name' => 'used_promo_code',
                    'value' => $promoCode->code,
                ]);
                // Calculate discount amount
                $discountAmount = round($promoCode->discount * ($request->credits) / 100, 3);
            }

            // Calculate base amount after applying discount
            $baseAmount = $request->credits - $discountAmount;

            // Calculate tax amount
            $taxAmount = 0;
            $tax_details = [];
            if($companyBillingDetails && Tax::exists()){
                $calculatedTaxDetail = Helper::calculateTaxAmount($baseAmount, $user->getCountryCodeValue(), $user->getRegionCodeValue());
                $tax_details = $calculatedTaxDetail['tax_applied'];
                $taxAmount = $calculatedTaxDetail['tax_amount'];
            }

            // Fetch the user's billing details
            $billingDetail = $user->billingDetail;

            // Construct address details using helper function
            $companyAddress = $this->getDetails($companyBillingDetails);
            $billingAddress = $this->getDetails($billingDetail);

            // Create transaction instance
            $transaction = $user->transactions()->create([
                'promo_code_id' => isset($promoCode)?$promoCode->id:null,
                'service' => 'Add wallet credits.',
                'base_amount' => $baseAmount,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount, 
                'final_amount' => $baseAmount + $taxAmount,
                'payment_gateway' => $request->payment_gateway,
                'key' => Helper::generateUniqueToken(32, 'transactions', 'key'),
                'company_address' => $companyAddress,
                'company_tax_numbers' => $companyBillingDetails ? $companyBillingDetails->tax_numbers : null,
                'tax_details' => $tax_details,
                'address' => $billingAddress,
                'tax_numbers' => $billingDetail ? $billingDetail->tax_numbers : null,
                'status' => 2,
            ])->refresh(); 

            // Success response: Transaction created
            return response()->json([
                "message" => "Please wait while we redirect you to complete the payment.",
                "transaction" => $transaction, 
            ],200);   
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ],500);
        }
    }

    public function checkout(Request $request)
    {
        // Retrieve the billing details
        $companyBillingDetails = BillingDetail::first();

         // Determine the minimum credits required for a transaction
        $minimumCredits = 1;
        if($companyBillingDetails && $companyBillingDetails->minimum_recharge_amount > 0) {
            $minimumCredits = (double) $companyBillingDetails->minimum_recharge_amount;
        }

        // Validate incoming request data
        $request->validate([
            'credits' => ['required', 'numeric', 'min:' . $minimumCredits, new AmountFormatRule],
            'promo_code' => 'nullable|alpha_num',
            'promo_code_type' => 'nullable|string',
        ]);

        try{
            $user = auth()->user();

            $promoCode = null;
            $discountAmount = 0;
            $baseAmount = $credits = round($request->credits, 3);

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

                // Check if promo code is valid
                if($request->promo_code_type != null && $request->promo_code_type != $promoCode->type) {
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

                // Check if promo code is active
                if($promoCode->expires_at) {
                    $promoCode->active = $promoCode->expires_at->diffInHours(now(),false)<0?true:false;
                } else {
                    $promoCode->active = true;
                }

                if(isset($promoCode) && $promoCode->type == 'discount' && $promoCode->discount > 0){
                    // Calculate discount amount
                    $discountAmount = round($promoCode->discount * ($credits) / 100, 3);
                }
    
                // Calculate base amount after applying discount
                $baseAmount = $credits - $discountAmount;
            }

            $data = [];
            $data['sub_total'] = $credits;
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
                "message" => "Something went really wrong!",
            ], 500);
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
     * Execute the transaction identified by its key.
     *
     * @param string $key The unique key of the transaction.
     * @return \Illuminate\Http\JsonResponse Response indicating success or failure.
     */
    public function execute($key){
        // Check if the transaction with the provided key and status 1 (completed) exists
        if(Transaction::where('key', $key)->where('status', 1)->exists()){
            return response()->json([
                "message" => "Transaction already compeletd!",
            ],500);
        }

        // Check if the transaction with the provided key and status 0 (failed) exists
        if(Transaction::where('key',$key)->where('status', 0)->exists()) {
            // Unauthorized response
            return response()->json([
                'message' => "This transaction is failed. Please try again!"
            ], 500);
        }

        // Find the transaction by its key and ensure it's not already completed or failed etc
        if(!$transaction = Transaction::where('key', $key)->where('status', '!=', true)->first()){
            return response()->json([
                "message" => "No transaction found!",
            ],500);
        }
        
        // Retrieve user associated with the transaction
        $user = $transaction->user;

        // Retrieve payment provider associated with the transaction
        $provider = $transaction->payment_gateway;

        // Check if the payment provider is enabled
        if(!$paymentConfig = PaymentConfig::where('provider',$provider)->where('enabled', true)->first()){
            return response()->json([
                'message' => "$provider temporarily disabled!"
            ],500);
        }

        $currency = Helper::currency()["currency"];
        $finalAmount = Currency::formatAmount($transaction->final_amount, $currency);

        // Process payment based on the payment gateway
        if($provider == PaymentGatewayEnum::RAZORPAY()) {
            try {

                // Create order with Razorpay
                $api = new Api($paymentConfig->client_id, $paymentConfig->client_secret);
                $order = $api->order->create(array(
                    'receipt' => "$transaction->id", 
                    'amount' => (int)($finalAmount*100),
                    'currency' => $currency,
                ));

                // Update transaction with payment link
                $transaction->payment_link = $order['id'];
                $transaction->save();
            } catch (\Exception $e) {
                // ❌ Error response: Handle and respond to any exceptions
                report($e);
                return response()->json([
                    "message" => $e->getMessage() ? $e->getMessage() : "Something went really wrong!",
                ],500);
            }
        } elseif ($provider == PaymentGatewayEnum::PAYPAL()){
            try {
                // Set PayPal API credentials
                config()->set('paypal.mode',$paymentConfig->mode);
                config()->set("paypal.$paymentConfig->mode.client_id", $paymentConfig->client_id);
                config()->set("paypal.$paymentConfig->mode.client_secret", $paymentConfig->client_secret);

                // Retrieve PayPal configuration
                $config = config('paypal');

                // Create PayPal client instance
                $provider = new PayPalClient;
                $provider->setApiCredentials(config('paypal'));
                $paypalToken = $provider->getAccessToken();
                
                // Create order with PayPal
                $response = $provider->createOrder([
                    "intent" => "CAPTURE",
                    "application_context" => [
                        "return_url" => url(config('app.url')."/verify-transaction/$transaction->key?state=".request()->get('state')),
                        "cancel_url" => url(config('app.url')."/verify-transaction/$transaction->key?state=".request()->get('state')),
                    ],
                    "purchase_units" => [
                        0 => [
                            "amount" => [
                                "currency_code" => $currency,
                                "value" => $finalAmount,
                            ]
                        ]
                    ]
                ]);

                // Check if order creation was successful
                if (isset($response['id']) && $response['id'] != null) {
                    // redirect to approve href
                    foreach ($response['links'] as $links) {
                        if ($links['rel'] == 'approve') {
                            // Update transaction with payment link
                            $transaction->payment_link = $links['href'];
                            $transaction->save();
                        }
                    }
                    // Check payment link is set
                    if(!$transaction->payment_link) {
                        return response()->json([
                            "message" => "Something went really wrong! Please try again after some times!",
                        ],500);
                    }
                } else {
                    return response()->json([
                        "message" => "Something went wrong!!",
                    ],500);
                }
            } catch (\Exception $e) {
                report($e);
                return response()->json([
                    "message" => $e->getMessage() ? $e->getMessage() : "Something went really wrong! Please try again after some times!"
                ],500);
            }
        } elseif($provider == PaymentGatewayEnum::STRIPE()){
            try {
                // Retrieve user associated with the transaction
                $user = $transaction->user;

                // Create or get Stripe customer for the user
                $user->createOrGetStripeCustomer();

                // Update Stripe customer with address information
                $user->updateStripeCustomer([
                    'address' => [
                        'state' => $user->getRegionNameValue(),
                        'country' => $user->getCountryCodeValue(),
                    ],
                    'shipping' => [
                        'name' => $user->name,
                        'address' => [
                            'line1' => $user->getRegionNameValue(),
                            'country' => $user->getCountryCodeValue(),
                        ]
                    ],
                ]);

                // Prepare Stripe checkout charge parameters
                $checkoutCharge = $user->checkoutCharge($finalAmount * 100, 'Add wallet credits', null, [
                    'mode' => 'payment',
                    'currency' => $currency,
                    'billing_address_collection' => 'auto',
                    'success_url' => url(config('app.url')."/verify-transaction/$transaction->key?state=".request()->get('state')."&session_id={CHECKOUT_SESSION_ID}"),
                    'cancel_url' => url(config('app.url')."/verify-transaction/$transaction->key?state=".request()->get('state')."&session_id={CHECKOUT_SESSION_ID}"),
                ]);

                // Check if checkout charge URL is available
                if($checkoutCharge->url) {
                    // Update transaction with payment link
                    $transaction->payment_link = $checkoutCharge->url;
                    $transaction->save();
                }else{
                    return response()->json([
                        "message" => "Something went really wrong!",
                    ],500);
                }
            } catch (\Exception $e) {
                // ❌ Error response: Handle and respond to any exceptions
                report($e);
                return response()->json([
                    "message" => $e->getMessage() ? $e->getMessage() : "Something went really wrong! Please try again after some times!",
                ],500);
            }
        } elseif($provider == PaymentGatewayEnum::CASHFREE()){
            try {

                $requestData = [
                    'link_id' => $transaction->key,
                    'link_amount' => $finalAmount,
                    'link_currency' => $currency,
                    'link_purpose' => $transaction->service,
                    'customer_details' => [
                        'customer_email' => $user->email,
                        'customer_phone' => $user->mobile_no ?? "9876543210",
                        'customer_name' => $user->name
                    ],
                    'link_meta' => [
                        'return_url' => url(config('app.url')."/verify-transaction/$transaction->key?state=".request()->get('state'))
                    ]
                ];

                $response = Client::cashfree("links", 'POST', $paymentConfig->client_id, $paymentConfig->client_secret, $paymentConfig->mode, $requestData);

                if (isset($response['error'])) {
                    // Error response
                    return response()->json([
                        "message" => $response['message'],
                    ], 500);
                }

                $paymentLink = json_decode($response);
                if ($paymentLink && isset($paymentLink->link_url)) {
                    $transaction->payment_link = $paymentLink->link_url;
                    $transaction->save();
                } else {
                    return response()->json([
                        "message" => "Something went really wrong."
                    ], 500);
                }
            } catch (\Exception $e) {
                report($e);
                return response()->json([
                    "message" => $e->getMessage() ? $e->getMessage() : "Something went really wrong! Please try again after some times!"
                ],500);
            }
        } else {
            return response()->json([
                "message" => "Payment gateway not found!"
            ],404);
        }

        unset($transaction->user);

        // ✅ Success response
        return response()->json([
            'message' => "Payment link has been created successfully.",
            'transaction' => $transaction
        ],200);
    }

    /**
     * Verify the payment for the transaction with the given key.
     *
     * @param string $key The unique key of the transaction.
     * @return \Illuminate\Http\JsonResponse Response indicating success or failure.
     */
    public function verify($key){
        try {
             // Find transaction by key 
            if(!$transaction = Transaction::where('key',$key)->where('status','!=',true)->first()){
                return response()->json([
                    'message' => "No transaction found!"
                ],404);
            }

            // Retrieve payment gateway provider for the transaction
            $provider = $transaction->payment_gateway; 

            // Check if payment gateway configuration is enabled
            if(!$paymentConfig = PaymentConfig::where('provider',$provider)->where('enabled', true)->first()){
                return response()->json([
                    'message' => "$provider configuration is disabled!"
                ],500);
            }
            
            // Process payment verification based on the payment gateway
            if($provider == PaymentGatewayEnum::RAZORPAY()){
                // Retrieve Razorpay payment ID from the request
                $razorpay_payment_id = request()->get('razorpay_payment_id');
                
                // ❌ Error response Check if razorpay_payment_id Exist!
                if ($razorpay_payment_id == "") {
                    $transaction->update(["status" => 0]);

                    return response()->json([
                        'message' => "Payment failed! If the amount is already deducted from your account, It will be refunded within 7 business days!"
                    ],500);
                }

                // Initialize Razorpay API instance
                $api = new Api($paymentConfig->client_id, $paymentConfig->client_secret);
                
                // Fetch payment details from Razorpay
                $payment = $api->payment->fetch($razorpay_payment_id);

                // Check if payment is captured successfully
                if (!empty($payment) && $payment['status'] == 'captured') {
                    // Update Transaction
                    $transaction->status = 1;
                    $transaction->payment_link = null;
                    $transaction->transaction_id = $payment['id'];
                    $transaction->save();                          

                    // Perform action on account based on purpose of transaction
                    $this->updateAccount($transaction);
                } else {
                    $transaction->update(["status" => 0]);

                     // ❌ Error response: Payment failed
                    return response()->json([
                        'message' => "Payment failed! If the amount is already deducted from your account, It will be refunded within 7 business days!"
                    ],500);
                }
            } elseif($provider == PaymentGatewayEnum::PAYPAL()) {
                
                // Get Important Credentials from Response
                $payer_id= request()->get('payer_id');
                $token= request()->get('token');

                // ❌ Error response Check if Payer ID and Token Exist!
                if ($payer_id == "" || $token == "") {
                    $transaction->update(["status" => 0]);
                    
                    return response()->json([
                        'message' => "Payment failed! If the amount is already deducted from your account, It will be refunded within 7 business days!"
                    ],500);
                }

                // Set PayPal API credentials
                config()->set('paypal.mode',$paymentConfig->mode);
                config()->set("paypal.$paymentConfig->mode.client_id", $paymentConfig->client_id);
                config()->set("paypal.$paymentConfig->mode.client_secret", $paymentConfig->client_secret);

                // Initialize PayPal client instance
                $provider = new PayPalClient;
                $provider->setApiCredentials(config('paypal'));
                $provider->getAccessToken();
                
                // Capture payment order with PayPal
                $response = $provider->capturePaymentOrder(request()->get('token'));
                
                 // Check if payment is completed successfully
                if (isset($response['status']) && $response['status'] == 'COMPLETED') {

                    // Update Transaction
                    $transaction->status = 1;
                    $transaction->payment_link = null;
                    $transaction->transaction_id = $response['id'];
                    $transaction->save();

                    // Perform action on account based on purpose of transaction
                    $this->updateAccount($transaction);
                } else {
                    $transaction->update(["status" => 0]);

                    // ❌ Error response: Payment failed
                    return response()->json([
                        "message" => "Something went really wrong! Please try again after some times!",
                    ],500);
                }
            } else if($provider == PaymentGatewayEnum::STRIPE()) {
                // Retrieve session ID from the request
                $sessionId = request()->get('session_id');
                
                 // Error response: if session ID not exists
                if ($sessionId=="") {
                    $transaction->update(["status" => 0]);
                    
                    return response()->json([
                        'message' => "Payment failed! If the amount is already deducted from your account, It will be refunded within 7 business days!"
                    ],500);
                }

                // Retrieve user associated with the transaction
                $user = $transaction->user;

                // Create or get Stripe customer for the user
                $user->createOrGetStripeCustomer();

                // Retrieve payment session details from Stripe
                $payment = $user->stripe()->checkout->sessions->retrieve($sessionId);

                 // Check if payment is complete
                if($payment->status == "complete") {
                    // Update Transaction status and details
                    $transaction->status = 1;
                    $transaction->payment_link = null;
                    $transaction->transaction_id = $payment->id;
                    $transaction->save();

                    // Perform action on account based on purpose of transaction
                    $this->updateAccount($transaction);
                } else {
                    $transaction->update(["status" => 0]);

                    // ❌ Error response
                    return response()->json([
                        'message' => "Payment failed! If the amount is already deducted from your account, It will be refunded within 7 business days!"
                    ],500);
                }
            } else if($provider == PaymentGatewayEnum::CASHFREE()) {
                $response = Client::cashfree("links/{$transaction->key}", "GET", $paymentConfig->client_id, $paymentConfig->client_secret, $paymentConfig->mode);

                if(isset($response['error'])){
                    $transaction->update(["status" => 0]);

                    return response()->json([
                        'message' => $response['message']
                    ],500);
                }

                $paymentDetail = json_decode($response);

                if($paymentDetail->link_status == "PAID"){
                    $transaction->status = 1;
                    $transaction->payment_link = null;
                    $transaction->transaction_id = $paymentDetail->cf_link_id;
                    $transaction->save();

                    // Perform action on account based on purpose of transaction
                    $this->updateAccount($transaction);
                }else{
                    $transaction->update(["status" => 0]);

                    return response()->json([
                        'message' => "Payment failed! If the amount is already deducted from your account, It will be refunded within 7 business days!"
                    ], 500);
                }
            }

            // Update promo code use in transaction
            if($promoCode = PromoCode::find($transaction->promo_code_id)) {
                $promoCode->usage = $promoCode->usage + 1;
                if($promoCode->usable){
                    $promoCode->expires_at = $promoCode->usable <= $promoCode->usage?now():$promoCode->expires_at;
                }
                $promoCode->save();
            }

            // Send transaction successfull mail to user
            \Mail::to($transaction->user)->queue((new TransactionSuccessful($transaction))->onQueue('default'));

            $currency = Helper::currency();

            // Store Activity
            Helper::createActivity($transaction->user, 'Transaction', 'Create', 'Transaction of '. $currency['currency_symbol'] . $transaction->final_amount . ' has been created.');

            $transaction = $transaction->refresh();

             // ✅ Success response
            return response()->json([
                "message" => "The payment has been verified successfully.",
            ],200);
        } catch (\Exception $e) {
            // ❌ Error response
            report($e);
            return response()->json([
                "message" => $e->getMessage() ? $e->getMessage() : "Something went really wrong!",
            ],500);
        }
    }

    /**
     * Update the user account after a successful transaction.
     *
     * @param Transaction $transaction The transaction object to update account for.
     * @return bool Indicates whether the account update was successful or not.
     */
    public function updateAccount($transaction){
        try {
            // Update transaction paid timestamp
            $transaction->update(['paid_at' => now()]);

            // Retrieve user associated with the transaction
            $user = $transaction->user;

            // Update user credits with transaction amount
            $user->credits = $user->credits + $transaction->base_amount + $transaction->discount_amount;

            // If the transaction used a promo code, mark other transactions with the same promo code as inactive
            if($transaction->promo_code_id){
                $user->transactions()
                    ->where('promo_code_id', $transaction->promo_code_id)
                    ->where('status', '!=', 1)
                    ->update(['status' => 0]);
            }

            // Save user account changes
            $user->save();

            // Remove User Credit Reminders if the user has credits
            if ($user->credits > 0 && $user->creditReminders()->exists()) {
                $user->creditReminders()->delete();
            }

            // Return true indicating successful account update
            return true;
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Coupon  $promoCode
     * @return \Illuminate\Http\Response
     */
    public function promocodeShow($promo_code)
    {
        try {

            $user = auth()->user();

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
}
