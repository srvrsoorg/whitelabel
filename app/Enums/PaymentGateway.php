<?php

namespace App\Enums;

use ArchTech\Enums\Values;
use ArchTech\Enums\InvokableCases;

enum PaymentGateway: string
{
    use InvokableCases, Values;

    case STRIPE = 'Stripe';
    case RAZORPAY = 'Razorpay';
    case PAYPAL = 'Paypal';
    case PAYTR = 'Paytr';
}