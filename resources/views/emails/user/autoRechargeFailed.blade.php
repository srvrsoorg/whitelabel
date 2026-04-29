@component('mail::message')

# Dear {{ $user->name }},

Your auto recharge attempt on **{{ config('app.title') }}** could not be completed.

**Reason:** {{ $reason }}

Please review your default payment method and auto recharge settings, then try again.

@component('mail::button', ['url' => url('/billing/auto-recharge'), 'color' => 'primary'])
Open Auto Recharge Settings
@endcomponent

If you need help, you can raise a [support ticket]({{ url('/tickets') }}).

@endcomponent
