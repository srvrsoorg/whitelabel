@component('mail::message')
Dear {{ $user->name }},

We are pleased to inform you that the server you recently set up using {{ config('app.title') }} is now ready for hosting applications and databases.

For security reasons, a root password for {{ $server->name }} was set during the setup process. We recommend keeping this password in a secure location for any future administrative tasks or data recovery.

To assist you in managing your server credentials, we have listed the relevant information below:

@component('mail::table')
| **Detail**              | **Information**                      |
|:------------------------|:-------------------------------------|
| **Server IP**           | {{ $server->ip }}                    |
| **Username**            | {{ $server->username }}              |
| **Password**            | {{ $server->password }}              |
| **Database Type**       | {{ ucfirst($server->database_type) }}|
| **Database Username**   | {{ $server->database_username }}     |
| **Database Password**   | {{ $server->database_password }}     |
| **Redis Password**      | {{ $server->redis_password }}        |
@endcomponent

We understand that it can be challenging to work with a new platform. If you have any questions or need further assistance, kindly contact your admin.

Thank you for choosing {{ config('app.title') }}.
@endcomponent