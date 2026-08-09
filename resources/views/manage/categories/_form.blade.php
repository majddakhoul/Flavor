@php($category = $category ?? null)

            <div class="grid grid-2">
                <x-field name="name" :label="__('app.name')" required>
                    <x-input name="name" :value="$category?->name" />
                </x-field>
                <x-field name="parent_id" :label="__('app.parent_category')">
                    <x-select name="parent_id" :options="$parents" :selected="$category?->parent_id" :placeholder="__('app.none')" />
                </x-field>
            </div>

            <x-field name="description" :label="__('app.description')">
                <x-textarea name="description" :value="$category?->description" rows="3" />
            </x-field>

            <x-translations-tabs :model="$category" :fields="['name' => __('app.name'), 'description' => __('app.description')]" />
