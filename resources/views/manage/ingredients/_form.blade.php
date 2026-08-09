@php($ingredient = $ingredient ?? null)

            <div class="grid grid-2">
                <x-field name="name" :label="__('app.name')" required><x-input name="name" :value="$ingredient?->name" /></x-field>
                <x-field name="unit" :label="__('app.unit')" :hint="__('app.unit_hint')" required><x-input name="unit" :value="$ingredient?->unit" /></x-field>
            </div>

            <div class="grid grid-2">
                <x-field name="stock_quantity" :label="__('app.stock')" required>
                    <x-input name="stock_quantity" type="number" min="0" :value="$ingredient?->stock_quantity ?? 0" />
                </x-field>
                <x-field name="unit_cost" :label="__('app.unit_cost')" required>
                    <x-input name="unit_cost" type="number" min="0" :value="$ingredient?->unit_cost ?? 0" />
                </x-field>
            </div>

            <label class="switch" style="margin-bottom:var(--space-3)">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $ingredient?->is_active ?? true))>
                <span class="switch__track"></span>
                <span>{{ __('app.active') }}</span>
            </label>

            <x-translations-tabs :model="$ingredient" :fields="['name' => __('app.name'), 'unit' => __('app.unit')]" />
