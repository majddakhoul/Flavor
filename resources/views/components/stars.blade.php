@props(['value' => 0, 'count' => null])
<span class="stars" title="{{ number_format((float) $value, 1) }}">
    @for ($star = 1; $star <= 5; $star++)
        <svg viewBox="0 0 24 24" class="{{ $star <= round($value) ? '' : 'is-off' }}" aria-hidden="true">
            <path d="m12 3.6 2.6 5.3 5.9.9-4.3 4.1 1 5.8-5.2-2.7-5.2 2.7 1-5.8L3.5 9.8l5.9-.9z"/>
        </svg>
    @endfor
    @if ($count !== null)<span class="small muted">({{ $count }})</span>@endif
</span>
