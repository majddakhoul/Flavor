@props(['action', 'label' => null, 'confirm' => null, 'method' => 'DELETE'])
<form method="POST" action="{{ $action }}" data-confirm="{{ $confirm ?? __('app.confirm_delete') }}" style="display:inline">
    @csrf
    @method($method)
    <button type="submit" class="btn btn--danger btn--sm" title="{{ $label ?? __('app.delete') }}">
        <x-icon name="trash" />
        @if ($label)<span>{{ $label }}</span>@endif
    </button>
</form>
