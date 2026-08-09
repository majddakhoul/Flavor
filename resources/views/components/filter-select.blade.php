@props(['name', 'label', 'options' => [], 'placeholder' => null])
<div class="field">
    <label class="field__label" for="filter-{{ $name }}">{{ $label }}</label>
    <select class="select" id="filter-{{ $name }}" name="filters[{{ $name }}]" data-auto-submit>
        <option value="">{{ $placeholder ?? __('app.all') }}</option>
        @foreach ($options as $key => $label)
            @php($optionValue = is_array($label) ? $label['value'] : $key)
            @php($optionLabel = is_array($label) ? $label['label'] : $label)
            <option value="{{ $optionValue }}" @selected((string) request("filters.$name") === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
</div>
