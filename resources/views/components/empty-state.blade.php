@props(['illustration' => 'empty-box', 'title', 'description' => null])
<div class="empty">
    <img src="{{ asset('assets/img/illustrations/' . $illustration . '.svg') }}" alt="">
    <h3>{{ $title }}</h3>
    @if ($description)<p>{{ $description }}</p>@endif
    {{ $slot }}
</div>
