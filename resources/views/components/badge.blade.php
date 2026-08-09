@props(['tone' => 'muted', 'plain' => false])
<span {{ $attributes->merge(['class' => 'badge badge--' . $tone . ($plain ? ' badge--plain' : '')]) }}>{{ $slot }}</span>
