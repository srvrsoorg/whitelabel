@component('mail::message')

# Dear {{ $user->name }},

This is a friendly reminder that your **{{ config('app.title') }}** credits are **below your set minimum credits of ${{ $user->reminder_minimum_credit }}**.

Currently, your available credits are **${{ $availableCredits }}**. To prevent any interruptions in your services, please add more credits to your account.

@component('mail::button', ['url' => url('/billing/wallet'), 'color' => 'primary'])
Add Credits
@endcomponent

@if (strtoupper((string) $user->getCountryCodeValue()) !== 'IN')
You can also set up auto recharge so credits are added automatically when balance drops.

@component('mail::button', ['url' => url('/billing/auto-recharge'), 'color' => 'primary'])
Setup Auto Recharge
@endcomponent
@endif

Keeping a sufficient balance ensures smooth server management and uninterrupted service.

If you have any questions or need further assistance, please raise a [support ticket]({{ url('/tickets') }}) from your dashboard.

Thanks for choosing **{{ config('app.title') }}**!

@endcomponent