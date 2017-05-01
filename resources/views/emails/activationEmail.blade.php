@component('mail::message')
# Introduction

Welcome to Room Rent {{$user->name}}.
Please click the button to activate the account.

@component('mail::button', ['url' => $url])
	Register
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
