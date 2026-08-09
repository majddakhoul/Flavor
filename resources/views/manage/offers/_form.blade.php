@php($offer = $offer ?? null)

<x-field name="title" :label="__('app.title')" required><x-input name="title" :value="$offer?->title" /></x-field>

<x-field name="description" :label="__('app.description')" required>
    <x-textarea name="description" :value="$offer?->description" rows="3" />
</x-field>

<div class="grid grid-3">
    <x-field name="discount_amount" :label="__('app.discount')" required>
        <x-input name="discount_amount" type="number" min="0" max="100" :value="$offer?->discount_amount ?? 10" />
    </x-field>
    <x-field name="start_date" :label="__('app.starts')" required>
        <x-input name="start_date" type="date" :value="$offer?->start_date?->toDateString() ?? now()->toDateString()" />
    </x-field>
    <x-field name="end_date" :label="__('app.ends')" required>
        <x-input name="end_date" type="date" :value="$offer?->end_date?->toDateString() ?? now()->addWeek()->toDateString()" />
    </x-field>
</div>

<label class="switch" style="margin-bottom:var(--space-3)">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $offer?->is_active ?? true))>
    <span class="switch__track"></span>
    <span>{{ __('app.active') }}</span>
</label>

<x-card :title="__('app.bundle_contents')" style="margin-bottom:var(--space-3)">
    <div class="stack" style="max-height:420px;overflow:auto">
        @foreach ($meals as $meal)
            @php($current = $offer?->meals->firstWhere('id', $meal->id))
            <div class="between" style="gap:.5rem">
                <span>{{ $meal->t('name') }} <span class="small price">@money($meal->price)</span></span>
                <input class="input" style="max-width:100px" type="number" min="0" max="100"
                       name="meals[{{ $meal->id }}]"
                       value="{{ old('meals.' . $meal->id, $current?->pivot?->quantity ?? 0) }}">
            </div>
        @endforeach
    </div>
</x-card>

<x-translations-tabs :model="$offer" :fields="['title' => __('app.title'), 'description' => __('app.description')]" />
