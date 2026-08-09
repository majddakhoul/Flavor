@php($table = $table ?? null)

            <div class="grid grid-2">
                <x-field name="table_number" :label="__('app.number')" required><x-input name="table_number" :value="$table?->table_number" /></x-field>
                <x-field name="location" :label="__('app.area')" required>
                    <x-select name="location" :options="$locations" :selected="$table?->location?->value" />
                </x-field>
            </div>

            <div class="grid grid-2">
                <x-field name="capacity" :label="__('app.capacity')" required><x-input name="capacity" type="number" min="1" max="40" :value="$table?->capacity ?? 4" /></x-field>
                <x-field name="price_per_hour" :label="__('app.price_per_hour')" required><x-input name="price_per_hour" type="number" min="0" :value="$table?->price_per_hour ?? 0" /></x-field>
            </div>

            <x-field name="description" :label="__('app.description')">
                <x-textarea name="description" :value="$table?->description" rows="2" />
            </x-field>

            <label class="switch" style="margin-bottom:var(--space-3)">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $table?->is_active ?? true))>
                <span class="switch__track"></span>
                <span>{{ __('app.active') }}</span>
            </label>

            <x-translations-tabs :model="$table" :fields="['description' => __('app.description')]" />
