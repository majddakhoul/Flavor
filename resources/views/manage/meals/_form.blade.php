@php($meal = $meal ?? null)

<div class="grid grid-2">
    <x-field name="name" :label="__('app.name')" required><x-input name="name" :value="$meal?->name" /></x-field>
    <x-field name="category_id" :label="__('app.category')" required>
        <x-select name="category_id" :options="$categories" :selected="$meal?->category_id" :placeholder="__('app.choose')" />
    </x-field>
</div>

<div class="grid grid-3">
    <x-field name="prep_time" :label="__('app.prep_time')" required>
        <x-input name="prep_time" type="time" :value="$meal ? \Illuminate\Support\Str::substr($meal->prep_time, 0, 5) : '00:20'" />
    </x-field>
    <x-field name="percentage" :label="__('app.margin')" :hint="__('app.margin_hint')" required>
        <x-input name="percentage" type="number" min="0" max="100" :value="$meal?->percentage ?? 35" />
    </x-field>
    <x-field name="availability" :label="__('app.availability')" required>
        <x-select name="availability" :options="$availabilities" :selected="$meal?->availability?->value" />
    </x-field>
</div>

<x-field name="description" :label="__('app.description')">
    <x-textarea name="description" :value="$meal?->description" rows="3" />
</x-field>

<label class="switch" style="margin-bottom:var(--space-3)">
    <input type="checkbox" name="is_vegetarian" value="1" @checked(old('is_vegetarian', $meal?->is_vegetarian ?? false))>
    <span class="switch__track"></span>
    <span>{{ __('app.vegetarian') }}</span>
</label>

<x-field name="image" :label="__('app.photo')" :hint="__('app.photo_hint')">
    <input class="input" type="file" name="image" accept="image/*">
</x-field>

@if ($meal?->picture)
    <div class="cluster" style="margin-bottom:var(--space-3)">
        <img src="{{ $meal->image_url }}" alt="" style="width:96px;height:96px;object-fit:cover;border-radius:var(--radius-sm)">
        <x-delete-form :action="route('manage.meals.photo.destroy', $meal)" :label="__('app.remove_photo')" />
    </div>
@endif

<x-translations-tabs :model="$meal" :fields="['name' => __('app.name'), 'description' => __('app.description')]" />
