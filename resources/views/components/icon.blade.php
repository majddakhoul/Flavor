@props(['name', 'size' => null])
<svg class="icon {{ $size === 'lg' ? 'icon--lg' : '' }}" aria-hidden="true" focusable="false">
    <use href="{{ asset('assets/img/icons/sprite.svg') }}#i-{{ $name }}"></use>
</svg>
