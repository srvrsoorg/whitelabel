@component('mail::message')

Hi {{ $user->name }},

We've noticed a successful login to your account using Two-Factor Authentication from the following device:

@component('mail::table')
| **Attribute**            | **Details**               |
| :----------------------- | :------------------------ |
| **Email Address**        | {{ $user->email }}        |
| **IP Address**           | {{ $ip }}                 |
| **Login Timestamp**      | {{ $twoFa->created_at }}  |
| **Web Browser**          | {{ $userAgent }}          |
@endcomponent

Your Verification Code is: **{{ $twoFa->token }}**

@component('mail::panel')
**Reminder:** This code is only valid for the next 5 minutes.
@endcomponent

**Please Note:** Never share your verification code with anyone.

This is an automated notification to help secure your account.

@endcomponent