@php
    $dueDate = \Carbon\Carbon::parse($creditReminders->first()->end_at)->format('Y-m-d')
    $showAutoRechargeButton = strtoupper((string) $user->getCountryCodeValue()) !== 'IN';
@endphp
@component('mail::message')
# Dear {{ $user->name }},

We hope this message finds you well.

This is a reminder that your **{{ config('app.title') }}** account currently has an outstanding credit balance of **@formatCurrency($user->credits)**. Kindly note that if the payment is not received by the due date, your **{{ config('app.title') }}** account services will be deactivated.

To avoid any service interruptions, make the payment by **{{ $dueDate }}**.

**Current Credit: @formatCurrency($user->credits)**

**Due date: {{ $dueDate }}**

If you have already made the payment, please disregard this message.

To add credits and proceed with the payment, click the button below. You will be redirected to your wallet, where you can click on "Add Credits" to complete the transaction.

@component('mail::button', ['url' => url('/billing/wallet')])
Add Credits
@endcomponent

@if ($showAutoRechargeButton)
You can also enable automatic top-ups to avoid service interruption in the future.

@component('mail::button', ['url' => url('/billing/auto-recharge')])
Setup Auto Recharge
@endcomponent
@endif

If you have any questions or need further assistance, please raise a [support ticket]({{ url('/tickets') }}) from your dashboard.

Thank you for choosing {{ config('app.title') }}!
@endcomponent
