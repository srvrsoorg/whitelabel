<?php

namespace App\Traits; 

use Laravel\Cashier\Billable as CashierBillable;
use Laravel\Cashier\Cashier;
use App\Models\Admin\Configuration\Payment;
use App\Enums\PaymentGateway as PaymentGatewayEnum;
use App\Http\Helper;

trait CustomBillable 
{
    use CashierBillable; 

    /** 
     * Get the default Stripe API options for the current Billable model. 
     * 
     * @param array $options 
     * @return array 
     */ 
     public function stripe(array $options = []) 
     { 
        // check if this model has it's own stripe_secret and use it 
        $stripe = Payment::where(['provider'=> PaymentGatewayEnum::STRIPE()])->first();
        if ($stripe->client_secret) {
            $options['api_key'] = $stripe->client_secret;
        }

        \Config::set('cashier.currency', Helper::currency()['currency']);

        return Cashier::stripe($options);
    }
}