@component('mail::message')

We heard that you lost your GitHub password. Sorry about that!

But don’t worry! You can use the following link within the next day to reset your password:

https://192.168.0.136:81/password_reset/

Thanks,<br>
{{ config('app.name') }}
@endcomponent
