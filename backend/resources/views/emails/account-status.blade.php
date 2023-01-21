<x-mail::message>
Hi, {{ $user->last_name }}!

@if($status == 'Declined')
We're sorry but your account was declined. Maybe you're not a member
of the university. If this was a mistake you may try contacting your
coach.
@else
Thank you for signing-up. Your account was approved by the coach and you can
now login to your account and get updated with the events.
@endif

Thanks,<br>
Administrator
</x-mail::message>
