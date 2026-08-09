@props(['name', 'type' => 'text', 'value' => null])
<input
    class="input"
    id="{{ $name }}"
    name="{{ $name }}"
    type="{{ $type }}"
    value="{{ old($name, $value) }}"
    @error($name) aria-invalid="true" @enderror
    {{ $attributes }}>
