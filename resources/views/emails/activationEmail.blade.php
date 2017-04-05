@component('mail::message')
# Introduction

Welcome to Room Rent {{$user->name}}.
Please click the button to activate the account.
@component('mail::button', ['url' => "http://192.168.0.136:81/api/activate/$user->activationToken"])
Register
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
