@component('mail::message')

# Dear {{ $user->name }},

We regret to inform you that your **{{ config('app.title') }}** account has been deactivated. and all associated servers and data have been deleted due to the failure to settle your outstanding bill. Unfortunately, recovery of the deleted servers and data is not possible.

We have sent multiple reminders to the email address associated with your account **{{ $user->email }}** regarding the outstanding credit balance and payment due date, but it seems we have been unable to reach you.

Kindly note that your account will remain deactivated until full payment is received.

To reactivate your account, please make the outstanding payment as soon as possible by clicking on the button below.

@component('mail::button', ['url' => url('/billing/wallet')])
Make Payment
@endcomponent

# Account Details:
**Current Credit: @formatCurrency($user->credits)**

@component('mail::table')
| Server Name            | IP Address     |
| :--------------------- | :------------- |
@foreach ($serverData as $server)
    @if ($server)
        | {{ $server['name'] }} | {{ $server['ip'] }} |
    @endif
@endforeach
@endcomponent

If you have already made the payment, please disregard this message.

If you have any questions or need additional assistance, please contact our support team.
@endcomponent
