<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Helpers\HelperFunctions;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ChartDemoWidget extends ChartWidget
{
    protected static ?string $heading = 'Here could be displayed some analysis info';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 1;

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    public static function canView(): bool
    {
        return HelperFunctions::isUser(Auth::user())->isAdmin() || HelperFunctions::isUser(Auth::user())->isManager();
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Demo',
                    'data' => [5,8,11,4,6,7,7,7,2,3,5,8,15,25,10,15,8,10],
                ],
            ],
            'labels' => [1, 2, 4, 5, 6, 7, 8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
