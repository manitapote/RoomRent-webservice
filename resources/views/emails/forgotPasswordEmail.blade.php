@component('mail::message')
{{$user->username}}<br>
We heard that you lost your RoomRent password. Sorry about that!

But don’t worry! You can use the following link within the next day to reset your password:

{{ $url }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
