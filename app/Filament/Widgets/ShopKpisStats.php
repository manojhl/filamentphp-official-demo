<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Shop\Customer;
use App\Models\Shop\Order;
use App\Models\Shop\OrderItem;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ShopKpisStats extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $startDate = filled($this->pageFilters['startDate'] ?? null)
            ? Carbon::parse($this->pageFilters['startDate'])
            : null;
        $endDate = filled($this->pageFilters['endDate'] ?? null)
            ? Carbon::parse($this->pageFilters['endDate'])
            : now();
        $orderStatuses = $this->pageFilters['orderStatuses'] ?? null;

        $orderQuery = Order::query()
            ->when($startDate, fn ($q) => $q->where('created_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('created_at', '<=', $endDate))
            ->when(filled($orderStatuses), fn ($q) => $q->whereIn('status', $orderStatuses));

        $totalOrders = $orderQuery->count();
        $totalRevenue = (float) $orderQuery->sum('total_price');
        $cancelledOrders = (clone $orderQuery)->where('status', OrderStatus::Cancelled)->count();

        $orderIds = (clone $orderQuery)->pluck('id');
        $totalItems = OrderItem::whereIn('order_id', $orderIds)->count();

        $totalCustomers = Customer::query()
            ->when($startDate, fn ($q) => $q->where('created_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('created_at', '<=', $endDate))
            ->count();

        $repeatCustomers = Customer::query()
            ->when($startDate, fn ($q) => $q->where('created_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('created_at', '<=', $endDate))
            ->has('orders', '>=', 2)
            ->count();

        $repeatRate = $totalCustomers > 0
            ? round(($repeatCustomers / $totalCustomers) * 100, 1)
            : 0;
        $avgItemsPerOrder = $totalOrders > 0
            ? round($totalItems / $totalOrders, 1)
            : 0;
        $cancellationRate = $totalOrders > 0
            ? round(($cancelledOrders / $totalOrders) * 100, 1)
            : 0;
        $revenuePerCustomer = $totalCustomers > 0
            ? round($totalRevenue / $totalCustomers, 2)
            : 0;

        return [
            Stat::make('Repeat Customer Rate', $repeatRate . '%')
                ->description($repeatCustomers . ' of ' . $totalCustomers . ' customers')
                ->descriptionIcon(Heroicon::ArrowPath)
                ->color('success'),
            Stat::make('Avg Items / Order', (string) $avgItemsPerOrder)
                ->description($totalItems . ' items across ' . $totalOrders . ' orders')
                ->descriptionIcon(Heroicon::ShoppingCart)
                ->color('info'),
            Stat::make('Cancellation Rate', $cancellationRate . '%')
                ->description($cancelledOrders . ' cancelled orders')
                ->descriptionIcon(Heroicon::XCircle)
                ->color($cancellationRate > 10 ? 'danger' : 'warning'),
            Stat::make('Revenue / Customer', '$' . number_format($revenuePerCustomer, 2))
                ->description('$' . number_format($totalRevenue, 0) . ' total revenue')
                ->descriptionIcon(Heroicon::CurrencyDollar)
                ->color('success'),
        ];
    }
}
