@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; color: #FFFFFF; text-decoration: none; font-weight: 700; font-size: 24px; letter-spacing: -0.5px;">
@if (trim($slot) === 'Laravel')
🎓 {{ config('app.name') }}
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
