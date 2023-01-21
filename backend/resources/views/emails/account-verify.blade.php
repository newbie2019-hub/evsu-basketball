<x-mail::message>
Hi, {{ $user->last_name }}! Thank you for signing-up.
Before you proceed please click the button
below to verify your email address.

<x-mail::button url="{{ env('FRONTEND_URL') . '#/verify?email='.$user->email}}">
Verify Account
</x-mail::button>

Thanks,<br>
Administrator
</x-mail::message>
