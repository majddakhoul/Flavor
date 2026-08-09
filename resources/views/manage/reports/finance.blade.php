@extends('layouts.manage')

@section('title', __('app.finance'))
@section('eyebrow', __('app.section_reports'))

@section('actions')
    <button class="btn btn--ghost btn--sm" type="button" onclick="window.print()"><x-icon name="print" />{{ __('app.print') }}</button>
@endsection

@section('content')
    <x-card>
        <form method="GET" action="{{ route('manage.reports.finance') }}" class="toolbar">
            <div class="field">
                <label class="field__label" for="from">{{ __('app.from') }}</label>
                <input class="input" id="from" name="from" type="date" value="{{ $from->toDateString() }}">
            </div>
            <div class="field">
                <label class="field__label" for="to">{{ __('app.to') }}</label>
                <input class="input" id="to" name="to" type="date" value="{{ $to->toDateString() }}">
            </div>
            <button class="btn btn--sm" type="submit"><x-icon name="chart" />{{ __('app.run_report') }}</button>
        </form>
    </x-card>

    <div class="grid grid-4">
        <x-stat :label="__('app.revenue')" :value="\App\Support\Money::format($report['revenue'])" icon="money" />
        <x-stat :label="__('app.payroll_total')" :value="\App\Support\Money::format($report['payroll'])" icon="users" tone="info" />
        <x-stat :label="__('app.maintenance')" :value="\App\Support\Money::format($report['maintenance'])" icon="tools" tone="danger" />
        <x-stat :label="__('app.net_result')" :value="\App\Support\Money::format($report['net'])" icon="receipt" tone="teal"
                :meta="__('app.orders_completed') . ': ' . $report['orders_completed']" />
    </div>

    <div class="split">
        <x-card :title="__('app.revenue_12_months')">
            <div class="chart"><canvas id="chart-revenue"></canvas></div>
        </x-card>

        <x-card :title="__('app.headcount_by_position')">
            <div class="stack">
                @foreach ($payroll['headcount_by_position'] as $position => $count)
                    <div class="between">
                        <span>{{ $position }}</span>
                        <b class="mono">{{ $count }}</b>
                    </div>
                @endforeach
            </div>
            <hr>
            <div class="between"><span>{{ __('app.stock_value') }}</span><b class="price">@money($report['stock_value'])</b></div>
        </x-card>
    </div>
@endsection

@push('head')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js" defer></script>
@endpush

@push('scripts')
<script>
    window.addEventListener('load', function () {
        const revenue = @json($monthly);
        FlavorCharts.render('chart-revenue', (c) => ({
            type: 'line',
            data: {
                labels: revenue.map((point) => point.label),
                datasets: [{ label: @json(__('app.revenue')), data: revenue.map((point) => point.value),
                    borderColor: c.primary, backgroundColor: c.primary + '33', fill: true, tension: .35 }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: c.border } }, x: { grid: { display: false } } } }
        }));
    });
</script>
@endpush
