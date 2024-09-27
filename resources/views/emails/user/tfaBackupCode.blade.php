@component('mail::message')

Hello {{ $userName }},

To help secure your account, we have generated Backup Codes for your Two-Factor Authentication. You can use these codes if you lose access to your Two-Factor Authentication device.

Below are your Backup Codes:

@component('mail::table')
| **Code**           | **Status**        |
| :----------------- | :---------------- |
@foreach($backupCodes as $backupCode)
| {{ $backupCode->backup_code }} | @if($backupCode->used) <span style="color:red">Unavailable</span> @else <span style="color:green">Available</span> @endif |
@endforeach
@endcomponent

**Important:** Do not share your backup codes with anyone.

@endcomponent