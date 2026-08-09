@props(['status'])
@php($tone = method_exists($status, 'tone') ? $status->tone() : 'muted')
<x-badge :tone="$tone === 'muted' ? 'muted' : $tone">{{ $status->label() }}</x-badge>
