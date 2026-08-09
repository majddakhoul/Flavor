@extends('layouts.manage')

@section('title', __('app.customers'))
@section('eyebrow', __('app.section_people'))

@section('content')
    <x-card>
        <x-toolbar :action="route('manage.customers.index')" :sorts="['created_at' => __('app.newest'), 'ban_date' => __('app.ban_date')]">
            <x-filter-select name="ban" :label="__('app.standing')" :options="['1' => __('app.banned'), '0' => __('app.in_good_standing')]" />
        </x-toolbar>

        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('app.customer') }}</th>
                    <th>{{ __('app.contact') }}</th>
                    <th>{{ __('app.area') }}</th>
                    <th>{{ __('app.standing') }}</th>
                    <th style="text-align:end">{{ __('app.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td class="cell-media">
                            <span class="avatar">{{ $customer->user?->initials }}</span>
                            <span><b>{{ $customer->user?->full_name }}</b><br>
                                <span class="small muted">{{ __('app.since') }} {{ $customer->created_at?->translatedFormat('M Y') }}</span></span>
                        </td>
                        <td class="small mono">{{ $customer->user?->email }}<br>{{ $customer->user?->phone }}</td>
                        <td>{{ $customer->user?->location?->label ?? '—' }}</td>
                        <td>
                            <x-badge :tone="$customer->is_banned ? 'danger' : 'success'">
                                {{ $customer->is_banned ? __('app.banned') : __('app.in_good_standing') }}
                            </x-badge>
                        </td>
                        <td>
                            <div class="table__actions">
                                <a class="btn btn--ghost btn--sm" href="{{ route('manage.customers.show', $customer) }}"><x-icon name="eye" /></a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-empty-state :title="__('app.no_customers')" :description="__('app.no_customers_hint')" /></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $customers->links() }}
    </x-card>
@endsection
