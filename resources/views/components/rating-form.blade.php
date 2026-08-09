@props(['action', 'current' => 0])
<form method="POST" action="{{ $action }}" class="cluster">
    @csrf
    <div class="rating-form">
        @foreach ([5, 4, 3, 2, 1] as $star)
            <input type="radio" id="{{ Str::slug($action) }}-{{ $star }}" name="stars" value="{{ $star }}" @checked((int) $current === $star)>
            <label for="{{ Str::slug($action) }}-{{ $star }}" title="{{ $star }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3.6 2.6 5.3 5.9.9-4.3 4.1 1 5.8-5.2-2.7-5.2 2.7 1-5.8L3.5 9.8l5.9-.9z"/></svg>
            </label>
        @endforeach
    </div>
    <button class="btn btn--sm btn--ghost" type="submit">{{ __('app.rate') }}</button>
</form>
