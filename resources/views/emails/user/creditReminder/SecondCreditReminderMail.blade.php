@php
	$endDate = \Carbon\Carbon::parse($creditReminders->first()->end_at)->format('Y-m-d')
@endphp

@component('mail::message')
# Dear {{ $user->name }},

We hope this message finds you well.

This is a reminder regarding the deactivation of your **{{ config('app.title') }}** account. Your current outstanding credit balance is **@formatCurrency($user->credits)**. Kindly note that your services for your **{{ config('app.title') }}** account are deactivated. If the payment is not received by the **{{ $endDate }}**, your servers and all associated data will be permanently deleted, and recovery of any deleted data will not be possible.

# Account Details:

**Current Credit: @formatCurrency($user->credits)**

# Server Details:
@component('mail::table')
| **Server Name**              | **IP Address**          |
|:-----------------------------|:------------------------|
@foreach ($creditReminders as $reminder)
    @if ($reminder->server)
		| {{ $reminder->server->name }} | {{ $reminder->server->ip }} |
	@endif
@endforeach
@endcomponent

To avoid deletion of your server and data, make the payment by **{{ $endDate }}**.

To add credits and proceed with the payment, click the button below. You will be redirected to your wallet, where you can click on "Add Credits" to complete the transaction.
@component('mail::button', ['url' => url('/billing/wallet')])
Add Credits
@endcomponent

If you have already made the payment, please disregard this message.

If you have any questions or need additional assistance, please contact our support team.

@endcomponent
