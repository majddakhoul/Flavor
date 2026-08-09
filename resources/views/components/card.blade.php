@props(['title' => null, 'action' => null, 'flush' => false])
<section {{ $attributes->merge(['class' => 'card' . ($flush ? ' card--flush' : '')]) }}>
    @if ($title)
        <header class="card__header">
            <h2 class="card__title">{{ $title }}</h2>
            @if ($action)<div class="cluster">{{ $action }}</div>@endif
        </header>
    @endif
    {{ $slot }}
</section>
