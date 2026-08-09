@php($employee = $employee ?? null)

            <div class="grid grid-2">
                <x-field name="first_name" :label="__('app.first_name')" required><x-input name="first_name" :value="$employee?->user?->first_name" /></x-field>
                <x-field name="last_name" :label="__('app.last_name')" required><x-input name="last_name" :value="$employee?->user?->last_name" /></x-field>
            </div>
            <div class="grid grid-2">
                <x-field name="email" :label="__('app.email')" required><x-input name="email" type="email" :value="$employee?->user?->email" /></x-field>
                <x-field name="phone" :label="__('app.phone')" :hint="__('app.phone_hint')" required><x-input name="phone" dir="ltr" :value="$employee?->user?->phone" /></x-field>
            </div>
            <div class="grid grid-2">
                <x-field name="gender" :label="__('app.gender')" required>
                    <x-select name="gender" :options="$genders" :selected="$employee?->user?->gender?->value" />
                </x-field>
                <x-field name="location_id" :label="__('app.location')">
                    <x-select name="location_id" :options="$locations" :selected="$employee?->user?->location_id" :placeholder="__('app.none')" />
                </x-field>
            </div>
            <div class="grid grid-2">
                <x-field name="national_id" :label="__('app.national_id')" required><x-input name="national_id" dir="ltr" :value="$employee?->national_id" /></x-field>
                <x-field name="position" :label="__('app.position')" required>
                    <x-select name="position" :options="$positions" :selected="$employee?->position?->value" />
                </x-field>
            </div>
            <div class="grid grid-2">
                <x-field name="salary" :label="__('app.salary')" required><x-input name="salary" type="number" min="0" :value="$employee?->salary ?? 0" /></x-field>
                <x-field name="bonus" :label="__('app.bonus')"><x-input name="bonus" type="number" min="0" :value="$employee?->bonus" /></x-field>
            </div>
            <div class="grid grid-2">
                <x-field name="hire_date" :label="__('app.hire_date')" required>
                    <x-input name="hire_date" type="date" :value="$employee?->hire_date?->toDateString() ?? now()->toDateString()" />
                </x-field>
                <x-field name="birth_date" :label="__('app.birth_date')" required>
                    <x-input name="birth_date" type="date" :value="$employee?->birth_date?->toDateString()" />
                </x-field>
            </div>
            <x-field name="password" :label="__('app.password')" :hint="__('app.password_employee_hint')">
                <x-input name="password" type="password" autocomplete="new-password" />
            </x-field>
            <x-field name="notes" :label="__('app.notes')"><x-textarea name="notes" :value="$employee?->notes" rows="2" /></x-field>
