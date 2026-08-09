@php($maintenance = $maintenance ?? null)

            <div class="grid grid-2">
                <x-field name="maintenance_item" :label="__('app.item')" required><x-input name="maintenance_item" :value="$maintenance?->maintenance_item" /></x-field>
                <x-field name="employee_id" :label="__('app.responsible')" required>
                    <x-select name="employee_id" :selected="$maintenance?->employee_id" :placeholder="__('app.choose')"
                              :options="$employees->mapWithKeys(fn ($employee) => [$employee->id => $employee->user?->full_name])->all()" />
                </x-field>
            </div>
            <div class="grid grid-2">
                <x-field name="price" :label="__('app.price')" required><x-input name="price" type="number" min="0" :value="$maintenance?->price ?? 0" /></x-field>
                <x-field name="discount" :label="__('app.discount')" :hint="__('app.discount_hint')"><x-input name="discount" type="number" min="0" max="100" :value="$maintenance?->discount ?? 0" /></x-field>
            </div>
            <x-field name="notes" :label="__('app.notes')"><x-textarea name="notes" :value="$maintenance?->notes" rows="3" /></x-field>
