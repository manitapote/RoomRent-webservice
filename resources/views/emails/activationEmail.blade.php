@component('mail::message')
# Introduction

Welcome to Room Rent {{$user->name}}.
Please click the button to activate the account.
@component('mail::button', ['url' => "http://192.168.0.136:8/api/activate/$user->activation_token"])
<!-- ['url' => "http://192.168.0.136:8/api/activate/$user->activation_token"]) -->
Register
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
