@extends('layouts.manage')

@section('title', $meal->t('name'))
@section('eyebrow', __('app.meals'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.meals.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
    <a class="btn btn--ghost btn--sm" href="{{ route('menu.show', $meal) }}"><x-icon name="eye" />{{ __('app.view') }}</a>
    <x-delete-form :action="route('manage.meals.destroy', $meal)" :label="__('app.delete')" />
@endsection

@section('content')
    <div class="split">
        <x-card :title="__('app.details')">
            <form method="POST" action="{{ route('manage.meals.update', $meal) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('manage.meals._form')
                <button class="btn" type="submit"><x-icon name="check" />{{ __('app.save_changes') }}</button>
            </form>
        </x-card>

        <div class="stack">
            <div class="ticket">
                <p class="eyebrow">{{ __('app.cost_breakdown') }}</p>
                <span class="ticket__code">M-{{ str_pad($meal->id, 4, '0', STR_PAD_LEFT) }}</span>
                <hr class="ticket__rule">
                <dl style="margin:0">
                    <div class="ticket__row"><dt>{{ __('app.prep_cost') }}</dt><dd>@money($meal->prep_cost)</dd></div>
                    <div class="ticket__row"><dt>{{ __('app.margin') }}</dt><dd>@money($meal->profit_margin)</dd></div>
                    <div class="ticket__row"><dt>{{ __('app.portions_left') }}</dt><dd>{{ $meal->max_portions }}</dd></div>
                </dl>
                <div class="ticket__total"><span>{{ __('app.price') }}</span><span class="price">@money($meal->price)</span></div>
            </div>

            <x-card :title="__('app.recipe')">
                <form method="POST" action="{{ route('manage.meals.recipe', $meal) }}">
                    @csrf
                    @method('PUT')
                    <div class="stack" style="max-height:460px;overflow:auto">
                        @foreach ($ingredients as $ingredient)
                            @php($current = $meal->ingredients->firstWhere('id', $ingredient->id))
                            <div class="between" style="gap:.5rem">
                                <span>
                                    {{ $ingredient->t('name') }}
                                    <span class="small muted">({{ $ingredient->t('unit') }} · {{ $ingredient->stock_quantity }})</span>
                                </span>
                                <input class="input" style="max-width:110px" type="number" step="0.1" min="0"
                                       name="ingredients[{{ $ingredient->id }}]"
                                       value="{{ old('ingredients.' . $ingredient->id, $current?->pivot?->quantity ?? 0) }}">
                            </div>
                        @endforeach
                    </div>
                    <button class="btn btn--block" style="margin-top:var(--space-3)" type="submit">
                        <x-icon name="check" />{{ __('app.save_recipe') }}
                    </button>
                </form>
            </x-card>
        </div>
    </div>
@endsection
