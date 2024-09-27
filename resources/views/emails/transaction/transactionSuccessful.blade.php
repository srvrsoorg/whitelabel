@component('mail::message')

Hello {{ $transaction->user->name }},

We’re pleased to inform you that your transaction on {{ config('app.title') }} has been successfully processed. Below are the details of your latest transaction for your reference:

@component('mail::table')
| **Detail**              | **Information**                     |
|:------------------------|:------------------------------------|
| **Invoice Number**      | {{ $transaction->id }}              |
| **Service**             | {{ $transaction->service }}         |
| **Amount**              | @formatCurrency($transaction->final_amount) |
| **Gateway**             | {{ $transaction->payment_gateway }} |
| **Transaction ID**      | {{ $transaction->transaction_id }}  |
@endcomponent

We appreciate your business and look forward to serving you again.

Thank you for choosing {{ config('app.title') }}!

@endcomponent