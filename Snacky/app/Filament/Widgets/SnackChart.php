<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Helpers\HelperFunctions;
use App\Models\Snack;
use DateInterval;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class SnackChart extends ChartWidget
{
    protected static ?string $heading = 'Snacks added per month';

    protected static ?int $sort = 4;

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
                    'label' => 'Snacks',
                    'data' => $this->chartDataSnacks(),
                ],
            ],
            'labels' => [1, 2, 4, 5, 6, 7, 8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function chartDataSnacks() :array
    {
        $users = Snack::where('created_at', '>', now()->sub(DateInterval::createFromDateString('30 days')))->orderBy('created_at', 'asc')->get();

        if (count($users) === 0) return array();
        
        $data = [];
        $count = 0;
        $interval = DateInterval::createFromDateString('1 day');

        $currentDate = $users->first()->created_at->add($interval);

        for ($i = 0; $i < count($users); $i++) {
            if ($users[$i]->created_at > $currentDate) {
                $currentDate = $currentDate->add($interval);
                $data[] = $count;
                $count = 0;
                $i--;
            } else {
                $count++;
            }
        }
        $data[] = $count;

        return $data;
    }
}
