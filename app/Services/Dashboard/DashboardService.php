<?php

namespace App\Services\Dashboard;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Meal;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\IngredientRepositoryInterface;
use App\Repositories\Contracts\MaintenanceRepositoryInterface;
use App\Repositories\Contracts\MealRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use App\Repositories\Contracts\TableRepositoryInterface;
use App\Services\Support\CacheService;
use Illuminate\Support\Carbon;

class DashboardService
{
    private const TAGS = ['dashboard'];

    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly ReservationRepositoryInterface $reservations,
        private readonly IngredientRepositoryInterface $ingredients,
        private readonly EmployeeRepositoryInterface $employees,
        private readonly CustomerRepositoryInterface $customers,
        private readonly MaintenanceRepositoryInterface $maintenances,
        private readonly MealRepositoryInterface $meals,
        private readonly TableRepositoryInterface $tables,
        private readonly CacheService $cache,
    ) {
    }

    public function overview(): array
    {
        return $this->cache->remember('dashboard', 'dashboard:overview', fn () => [
            'kpis' => $this->kpis(),
            'charts' => $this->charts(),
            'lists' => $this->lists(),
        ], self::TAGS);
    }

    public function kpis(): array
    {
        $payroll = $this->employees->payrollTotals();
        $maintenance = $this->maintenances->totals();

        return [
            'revenue_today' => $this->orders->revenueBetween(now()->startOfDay(), now()->endOfDay()),
            'revenue_month' => $this->orders->revenueBetween(now()->startOfMonth(), now()->endOfMonth()),
            'orders_today' => $this->orders->query()->today()->count(),
            'orders_open' => $this->orders->query()->open()->count(),
            'reservations_today' => $this->reservations->query()->whereDate('date', now()->toDateString())->count(),
            'occupancy' => $this->tables->occupancyToday(),
            'customers' => $this->customers->query()->count(),
            'customers_banned' => $this->customers->bannedCount(),
            'stock_value' => $this->ingredients->stockValue(),
            'stock_low' => $this->ingredients->query()->lowStock()->count(),
            'payroll' => $payroll,
            'maintenance' => $maintenance,
            'meals' => $this->meals->query()->count(),
            'meals_unavailable' => $this->meals->query()->where('availability', 'unavailable')->count(),
        ];
    }

    public function charts(): array
    {
        return [
            'orders_by_status' => $this->orders->countByStatus(),
            'orders_by_type' => $this->orders->countByType(),
            'reservations_by_status' => $this->reservations->countByStatus(),
            'daily_orders' => $this->dailySeries(),
            'monthly_revenue' => $this->monthlyRevenue(),
            'headcount' => $this->employees->headcountByPosition(),
            'menu_mix' => $this->menuMix(),
            'top_meals' => $this->meals->topSelling(6),
        ];
    }

    public function lists(): array
    {
        return [
            'latest_orders' => $this->orders->latest(6),
            'upcoming_reservations' => $this->reservations->upcoming(6),
            'low_stock' => $this->ingredients->lowStock(6),
            'newest_customers' => Customer::query()->with('user')->latest()->limit(5)->get(),
        ];
    }

    public function dailySeries(int $days = 14): array
    {
        $volume = $this->orders->dailyVolume($days)->keyBy('day');
        $series = [];

        for ($index = $days - 1; $index >= 0; $index--) {
            $day = now()->subDays($index)->toDateString();
            $series[] = [
                'label' => Carbon::parse($day)->translatedFormat('d M'),
                'value' => (int) ($volume[$day]->aggregate ?? 0),
            ];
        }

        return $series;
    }

    public function monthlyRevenue(int $months = 12): array
    {
        $series = [];

        for ($index = $months - 1; $index >= 0; $index--) {
            $month = now()->copy()->subMonths($index);
            $series[] = [
                'label' => $month->translatedFormat('M'),
                'value' => $this->orders->revenueBetween($month->copy()->startOfMonth(), $month->copy()->endOfMonth()),
            ];
        }

        return $series;
    }

    public function menuMix(): array
    {
        return Category::query()
            ->withCount('meals')
            ->having('meals_count', '>', 0)
            ->orderByDesc('meals_count')
            ->limit(8)
            ->get()
            ->mapWithKeys(fn (Category $category) => [$category->t('name') => $category->meals_count])
            ->all();
    }

    public function financeReport(Carbon $from, Carbon $to): array
    {
        $revenue = $this->orders->revenueBetween($from, $to);
        $payroll = $this->employees->payrollTotals();
        $maintenance = $this->maintenances->totals();
        $stock = $this->ingredients->stockValue();

        return [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'revenue' => $revenue,
            'payroll' => $payroll['total'],
            'maintenance' => $maintenance['total_price'],
            'stock_value' => $stock,
            'net' => $revenue - $payroll['total'] - $maintenance['total_price'],
            'orders_completed' => $this->orders->query()
                ->whereBetween('dated_at', [$from->toDateString(), $to->toDateString()])
                ->where('status', OrderStatus::Completed->value)
                ->count(),
        ];
    }

    public function menuPerformance(): array
    {
        return $this->cache->remember('reports', 'dashboard:menu-performance', fn () => [
            'top_meals' => $this->meals->topSelling(10),
            'unavailable' => Meal::query()->with('category')->where('availability', 'unavailable')->limit(10)->get(),
            'menu_mix' => $this->menuMix(),
        ], self::TAGS);
    }
}
