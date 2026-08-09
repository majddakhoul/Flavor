@extends('layouts.manage')

@section('title', __('app.dashboard'))
@section('eyebrow', __('app.today_at_a_glance'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.orders.index') }}"><x-icon name="orders" />{{ __('app.orders') }}</a>
    <a class="btn btn--sm" href="{{ route('manage.reports.finance') }}"><x-icon name="money" />{{ __('app.finance') }}</a>
@endsection

@section('content')
    <div class="grid grid-4">
        <x-stat :label="__('app.revenue_today')" :value="\App\Support\Money::format($kpis['revenue_today'])" icon="money"
                :meta="__('app.revenue_month') . ': ' . \App\Support\Money::format($kpis['revenue_month'])" />
        <x-stat :label="__('app.orders_today')" :value="$kpis['orders_today']" icon="orders" tone="info"
                :meta="__('app.open_orders') . ': ' . $kpis['orders_open']" />
        <x-stat :label="__('app.reservations_today')" :value="$kpis['reservations_today']" icon="calendar" tone="teal"
                :meta="__('app.occupancy') . ': ' . $kpis['occupancy']['busy'] . '/' . $kpis['occupancy']['total']" />
        <x-stat :label="__('app.low_stock')" :value="$kpis['stock_low']" icon="box"
                :tone="$kpis['stock_low'] > 0 ? 'danger' : 'brand'"
                :meta="__('app.stock_value') . ': ' . \App\Support\Money::format($kpis['stock_value'])" />
    </div>

    <div class="split">
        <x-card :title="__('app.orders_last_14_days')">
            <div class="chart"><canvas id="chart-daily"></canvas></div>
        </x-card>

        <x-card :title="__('app.floor_occupancy')">
            <p class="stat__value" style="font-size:var(--step-3)">{{ $kpis['occupancy']['rate'] }}%</p>
            <div class="progress"><span style="width:{{ $kpis['occupancy']['rate'] }}%"></span></div>
            <div class="grid grid-2" style="margin-top:var(--space-3)">
                <div><span class="stat__label">{{ __('app.busy') }}</span><b class="mono">{{ $kpis['occupancy']['busy'] }}</b></div>
                <div><span class="stat__label">{{ __('app.free') }}</span><b class="mono">{{ $kpis['occupancy']['free'] }}</b></div>
            </div>
            <hr>
            <div class="grid grid-2">
                <div><span class="stat__label">{{ __('app.customers') }}</span><b class="mono">{{ $kpis['customers'] }}</b></div>
                <div><span class="stat__label">{{ __('app.banned') }}</span><b class="mono">{{ $kpis['customers_banned'] }}</b></div>
                <div><span class="stat__label">{{ __('app.meals') }}</span><b class="mono">{{ $kpis['meals'] }}</b></div>
                <div><span class="stat__label">{{ __('app.unavailable') }}</span><b class="mono">{{ $kpis['meals_unavailable'] }}</b></div>
            </div>
        </x-card>
    </div>

    <div class="grid grid-3">
        <x-card :title="__('app.orders_by_status')">
            <div class="chart chart--sm"><canvas id="chart-status"></canvas></div>
        </x-card>
        <x-card :title="__('app.orders_by_type')">
            <div class="chart chart--sm"><canvas id="chart-type"></canvas></div>
        </x-card>
        <x-card :title="__('app.menu_mix')">
            <div class="chart chart--sm"><canvas id="chart-mix"></canvas></div>
        </x-card>
    </div>

    <div class="split">
        <x-card :title="__('app.revenue_12_months')">
            <div class="chart"><canvas id="chart-revenue"></canvas></div>
        </x-card>

        <x-card :title="__('app.top_selling')">
            <ol class="rank">
                @forelse ($charts['top_meals'] as $index => $meal)
                    <li>
                        <span class="rank__no">{{ $index + 1 }}</span>
                        <span class="rank__body">
                            <b>{{ $meal->name }}</b>
                            <span class="small muted">{{ $meal->sold }} {{ __('app.sold') }}</span>
                        </span>
                    </li>
                @empty
                    <p class="muted small">{{ __('app.no_sales_yet') }}</p>
                @endforelse
            </ol>
        </x-card>
    </div>

    <div class="split">
        <x-card :title="__('app.recent_orders')">
            <x-slot:action><a class="btn btn--ghost btn--sm" href="{{ route('manage.orders.index') }}">{{ __('app.see_all') }}</a></x-slot:action>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>{{ __('app.reference') }}</th>
                        <th>{{ __('app.customer') }}</th>
                        <th>{{ __('app.type') }}</th>
                        <th>{{ __('app.status') }}</th>
                        <th>{{ __('app.total') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($lists['latest_orders'] as $order)
                        <tr>
                            <td><a class="mono" href="{{ route('manage.orders.show', $order) }}">{{ $order->reference }}</a></td>
                            <td>{{ $order->customer?->user?->full_name ?? $order->employee?->user?->full_name ?? '—' }}</td>
                            <td>{{ $order->order_type->label() }}</td>
                            <td><x-status-badge :status="$order->status" /></td>
                            <td class="price">@money($order->total)</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="muted center">{{ __('app.no_orders') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <div class="stack">
            <x-card :title="__('app.upcoming_reservations')">
                <ul class="timeline">
                    @forelse ($lists['upcoming_reservations'] as $reservation)
                        <li>
                            <a class="mono" href="{{ route('manage.reservations.show', $reservation) }}">{{ $reservation->reservation_code }}</a>
                            <p class="small muted" style="margin:0">
                                {{ $reservation->date?->translatedFormat('d M') }} ·
                                {{ $reservation->party_size }} {{ __('app.guests') }} ·
                                {{ $reservation->tables->pluck('table_number')->implode(', ') }}
                            </p>
                        </li>
                    @empty
                        <p class="muted small">{{ __('app.no_reservations') }}</p>
                    @endforelse
                </ul>
            </x-card>

            <x-card :title="__('app.stock_watchlist')">
                @forelse ($lists['low_stock'] as $ingredient)
                    <div class="between" style="padding:.35rem 0">
                        <span>{{ $ingredient->t('name') }}</span>
                        <x-badge :tone="$ingredient->stock_quantity <= config('flavor.inventory.critical_stock_threshold') ? 'danger' : 'warning'">
                            {{ $ingredient->stock_quantity }} {{ $ingredient->t('unit') }}
                        </x-badge>
                    </div>
                @empty
                    <p class="muted small">{{ __('app.stock_healthy') }}</p>
                @endforelse
            </x-card>
        </div>
    </div>
@endsection

@push('head')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js" defer></script>
@endpush

@push('scripts')
<script>
    window.addEventListener('load', function () {
        const daily = @json($charts['daily_orders']);
        const revenue = @json($charts['monthly_revenue']);
        const byStatus = @json($charts['orders_by_status']);
        const byType = @json($charts['orders_by_type']);
        const mix = @json($charts['menu_mix']);

        FlavorCharts.render('chart-daily', (c) => ({
            type: 'line',
            data: {
                labels: daily.map((point) => point.label),
                datasets: [{
                    label: @json(__('app.orders')),
                    data: daily.map((point) => point.value),
                    borderColor: c.primary,
                    backgroundColor: c.primary + '33',
                    fill: true,
                    tension: .35,
                    pointRadius: 2
                }]
            },
            options: { scales: { y: { beginAtZero: true, grid: { color: c.border } }, x: { grid: { display: false } } },
                plugins: { legend: { display: false } } }
        }));

        FlavorCharts.render('chart-revenue', (c) => ({
            type: 'bar',
            data: {
                labels: revenue.map((point) => point.label),
                datasets: [{ label: @json(__('app.revenue')), data: revenue.map((point) => point.value), backgroundColor: c.primary, borderRadius: 6 }]
            },
            options: { scales: { y: { beginAtZero: true, grid: { color: c.border } }, x: { grid: { display: false } } },
                plugins: { legend: { display: false } } }
        }));

        FlavorCharts.render('chart-status', (c) => ({
            type: 'doughnut',
            data: {
                labels: Object.keys(byStatus),
                datasets: [{ data: Object.values(byStatus), backgroundColor: [c.warning, c.info, c.success, c.danger], borderWidth: 0 }]
            },
            options: { cutout: '62%', plugins: { legend: { position: 'bottom' } } }
        }));

        FlavorCharts.render('chart-type', (c) => ({
            type: 'doughnut',
            data: {
                labels: Object.keys(byType),
                datasets: [{ data: Object.values(byType), backgroundColor: [c.primary, c.secondary, c.purple], borderWidth: 0 }]
            },
            options: { cutout: '62%', plugins: { legend: { position: 'bottom' } } }
        }));

        FlavorCharts.render('chart-mix', (c) => ({
            type: 'bar',
            data: {
                labels: Object.keys(mix),
                datasets: [{ data: Object.values(mix), backgroundColor: c.secondary, borderRadius: 6 }]
            },
            options: { indexAxis: 'y', plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, grid: { color: c.border } }, y: { grid: { display: false } } } }
        }));
    });
</script>
@endpush
