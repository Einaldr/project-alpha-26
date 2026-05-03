<x-mail::message>
<img src="{{ $group->icon_url }}" width="100" height="100" style="border-radius: 50%;">
# {{ $group->name }}

# Hello!

You have been invited to join **{{ $group->name }} on {{ config('app.name') }}.

<x-mail::button :url="$url">
Accept Invitation
</x-mail::button>

Alternatively, you can use the link below:

{{$url}}

**Note:** The confirmation link will expire in {{$expiration}}.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
