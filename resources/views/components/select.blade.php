@props(['name', 'options' => [], 'selected' => null, 'placeholder' => null])
<select class="select" id="{{ $name }}" name="{{ $name }}" @error($name) aria-invalid="true" @enderror {{ $attributes }}>
    @if ($placeholder)<option value="">{{ $placeholder }}</option>@endif
    @foreach ($options as $key => $label)
        @php($optionValue = is_array($label) ? $label['value'] : $key)
        @php($optionLabel = is_array($label) ? $label['label'] : $label)
        <option value="{{ $optionValue }}" @selected((string) old($name, $selected) === (string) $optionValue)>{{ $optionLabel }}</option>
    @endforeach
</select>
