@props(['name', 'value' => null, 'rows' => 4])
<textarea class="textarea" id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" @error($name) aria-invalid="true" @enderror {{ $attributes }}>{{ old($name, $value) }}</textarea>
