@php($location = $location ?? null)

            <div class="grid grid-2">
                <x-field name="country" :label="__('app.country')" required><x-input name="country" :value="$location?->country" /></x-field>
                <x-field name="state" :label="__('app.state')" required><x-input name="state" :value="$location?->state" /></x-field>
            </div>
            <div class="grid grid-2">
                <x-field name="region" :label="__('app.region')" required><x-input name="region" :value="$location?->region" /></x-field>
                <x-field name="city" :label="__('app.city')" required><x-input name="city" :value="$location?->city" /></x-field>
            </div>
            <div class="grid grid-2">
                <x-field name="street" :label="__('app.street')"><x-input name="street" :value="$location?->street" /></x-field>
                <x-field name="delivery_time" :label="__('app.delivery_time')" :hint="__('app.delivery_time_hint')" required>
                    <x-input name="delivery_time" type="time" :value="$location ? \Illuminate\Support\Str::substr($location->delivery_time, 0, 5) : '00:45'" />
                </x-field>
            </div>
