@props(['name', 'label' => null, 'hint' => null, 'required' => false])
<div class="field">
    @if ($label)
        <label class="field__label" for="{{ $name }}">
            {{ $label }}@if ($required)<span aria-hidden="true" style="color:var(--color-danger)"> *</span>@endif
        </label>
    @endif
    {{ $slot }}
    @if ($hint)<span class="field__hint">{{ $hint }}</span>@endif
    @error($name)<span class="field__error">{{ $message }}</span>@enderror
</div>
