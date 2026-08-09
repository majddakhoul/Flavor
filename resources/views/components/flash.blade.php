@php($flash = session('flash'))
@if ($flash || $errors->any())
    <div class="flash">
        @if ($flash)
            <div class="toast toast--{{ $flash['tone'] ?? 'success' }}" role="status">
                <x-icon :name="($flash['tone'] ?? 'success') === 'success' ? 'check' : 'alert'" />
                <span>{{ $flash['message'] }}</span>
                <button type="button" class="toast__close" aria-label="{{ __('app.dismiss') }}"><x-icon name="close" /></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="toast toast--danger" role="alert">
                <x-icon name="alert" />
                <span>{{ $errors->first() }}</span>
                <button type="button" class="toast__close" aria-label="{{ __('app.dismiss') }}"><x-icon name="close" /></button>
            </div>
        @endif
    </div>
@endif
