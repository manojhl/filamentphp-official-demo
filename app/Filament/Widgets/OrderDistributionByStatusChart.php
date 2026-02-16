<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class OrderDistributionByStatusChart extends ChartWidget
{
    protected ?string $heading = 'Order distribution by status';

    protected static ?int $sort = 6;

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => [55, 25, 10, 7, 3],
                    'backgroundColor' => [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(201, 203, 207)',
                    ],
                ],
            ],
            'labels' => ['Completed', 'Processing', 'Pending', 'Cancelled', 'Refunded'],
        ];
    }
}
