@extends('layouts.manage')

@section('title', __('app.employees'))
@section('eyebrow', __('app.section_people'))

@section('actions')
    <a class="btn" href="{{ route('manage.employees.create') }}"><x-icon name="plus" />{{ __('app.new_employee') }}</a>
@endsection

@section('content')
    <div class="grid grid-4">
        <x-stat :label="__('app.headcount')" :value="$payroll['totals']['headcount']" icon="users" />
        <x-stat :label="__('app.salaries')" :value="\App\Support\Money::format($payroll['totals']['salaries'])" icon="money" tone="info" />
        <x-stat :label="__('app.bonuses')" :value="\App\Support\Money::format($payroll['totals']['bonuses'])" icon="star" tone="teal" />
        <x-stat :label="__('app.payroll_total')" :value="\App\Support\Money::format($payroll['totals']['total'])" icon="receipt" />
    </div>

    <x-card>
        <x-toolbar :action="route('manage.employees.index')" :sorts="['salary' => __('app.salary'), 'hire_date' => __('app.hire_date')]">
            <x-filter-select name="position" :label="__('app.position')" :options="\App\Enums\EmployeePosition::options()" />
        </x-toolbar>

        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('app.name') }}</th>
                    <th>{{ __('app.position') }}</th>
                    <th>{{ __('app.salary') }}</th>
                    <th>{{ __('app.bonus') }}</th>
                    <th>{{ __('app.seniority') }}</th>
                    <th style="text-align:end">{{ __('app.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($employees as $employee)
                    <tr>
                        <td><b>{{ $employee->user?->full_name }}</b><br><span class="small muted mono">{{ $employee->user?->email }}</span></td>
                        <td><x-badge tone="brand">{{ $employee->position->label() }}</x-badge></td>
                        <td><span class="mono">@money($employee->salary)</span></td>
                        <td><span class="mono">@money($employee->bonus)</span></td>
                        <td>{{ $employee->seniority_years }} {{ __('app.years') }}</td>
                        <td>
                            <div class="table__actions">
                                <a class="btn btn--ghost btn--sm" href="{{ route('manage.employees.edit', $employee) }}"><x-icon name="edit" /></a>
                                <x-delete-form :action="route('manage.employees.destroy', $employee)" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <x-empty-state illustration="empty-box" :title="__('app.no_employees')" :description="__('app.no_employees_hint')">
                            <a class="btn" href="{{ route('manage.employees.create') }}">{{ __('app.new_employee') }}</a>
                        </x-empty-state>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $employees->links() }}
    </x-card>
@endsection
