@component('mail::message')

Hello {{ $user->name }},

Thank you for signing up with {{ config('app.title') }}!

To complete your registration and start enjoying our services, please verify your email address by clicking the button below:

@component('mail::button', ['url' => url('/verify/'.$verificationToken), 'color' => 'success'])
Verify Your Email
@endcomponent

If you have trouble clicking the button, you can copy and paste this URL into your browser's address bar: [{{ url('/verify/'.$verificationToken) }}]({{ url('/verify/'.$verificationToken) }}).

Once you've verified your email, your account will be activated and ready to use.

Thank you for choosing {{ config('app.title') }}!

@endcomponent