@component('mail::message')

Hello {{ $user->name }},

We received a request to reset your password.

To reset your password, click the button below or visit this link: [{{ url('/reset-password/'.$token) }}]({{ url('/reset-password/'.$token) }}).

@component('mail::button', ['url' => url('/reset-password/'.$token), 'color' => 'success'])
Reset Password
@endcomponent

Please note: The link is valid for 60 minutes and can be used only once. If you did not request a password reset, you can safely ignore this email.

@endcomponent