@props(['action', 'sorts' => [], 'filters' => []])
<form method="GET" action="{{ $action }}" class="toolbar">
    <div class="field field--search">
        <label class="field__label" for="search">{{ __('app.search') }}</label>
        <input class="input" id="search" name="search" type="search" value="{{ request('search') }}" placeholder="{{ __('app.search_placeholder') }}">
    </div>

    {{ $slot }}

    @if ($sorts)
        <div class="field">
            <label class="field__label" for="sort_by">{{ __('app.sort_by') }}</label>
            <select class="select" id="sort_by" name="sort_by" data-auto-submit>
                <option value="">{{ __('app.newest') }}</option>
                @foreach ($sorts as $value => $label)
                    <option value="{{ $value }}" @selected(request('sort_by') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="field" style="max-width:130px">
            <label class="field__label" for="sort_dir">{{ __('app.direction') }}</label>
            <select class="select" id="sort_dir" name="sort_dir" data-auto-submit>
                <option value="desc" @selected(request('sort_dir') !== 'asc')>{{ __('app.descending') }}</option>
                <option value="asc" @selected(request('sort_dir') === 'asc')>{{ __('app.ascending') }}</option>
            </select>
        </div>
    @endif

    <div class="cluster">
        <button class="btn btn--sm" type="submit"><x-icon name="search" /><span>{{ __('app.apply') }}</span></button>
        <a class="btn btn--ghost btn--sm" href="{{ $action }}"><x-icon name="refresh" /><span>{{ __('app.reset') }}</span></a>
    </div>
</form>
