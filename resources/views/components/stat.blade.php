@props(['label', 'value', 'icon' => 'chart', 'meta' => null, 'tone' => 'brand'])
<article class="stat stat--{{ $tone }}">
    <span class="stat__icon"><x-icon :name="$icon" /></span>
    <div class="stat__body">
        <span class="stat__label">{{ $label }}</span>
        <strong class="stat__value">{{ $value }}</strong>
        @if ($meta)<span class="stat__meta">{{ $meta }}</span>@endif
    </div>
</article>
