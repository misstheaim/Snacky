<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Helpers\HelperFunctions;
use App\Models\Category;
use App\Models\Snack;
use App\Models\User;
use DateInterval;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = '100s';

    protected $total_snacks = 0;

    public static function canView(): bool
    {
        return HelperFunctions::isUser(Auth::user())->isAdmin() || HelperFunctions::isUser(Auth::user())->isManager();
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users:', User::count())
                ->description('Users per week:')
                ->chart($this->chartData('users', '1 week', '1 month'))
                ->color('info'),
            Stat::make('Total Snacks:', Snack::count())
                ->description('Snacks per day:')
                ->chart($this->chartData('snacks', '1 day', '1 month'))
                ->color('primary'),
            Stat::make('Total Categories:', Category::count())
                ->description('Total count of existing categories of snacks'),
            Stat::make('Most popular category', $this->mostPopularCategory())
                ->description("Total snacks: $this->total_snacks")
                ->descriptionIcon('heroicon-o-percent-badge', IconPosition::Before),
        ];
    }

    protected function mostPopularCategory() 
    {
        $category = Category::withCount('snacks')->orderBy('snacks_count', 'desc')->first();

        $this->total_snacks = $category->snacks_count;
        return $category->title_ru;
    }

    protected function chartData($tableName, $interval, $period) :array
    {
        $currentDate = now();
        $periodInterval = DateInterval::createFromDateString($period);
        $endDate = clone $currentDate;
        $startDate = $currentDate->sub($periodInterval);

        $rawSql = <<<SQL
        with recursive dates as (
            select date("$startDate") date
            union all
            select dates.date + interval $interval from dates where date < date("$endDate")
        )
        select  count($tableName.created_at) as cn
        from dates
        left join $tableName on date($tableName.created_at) = dates.date
        group by dates.date
        SQL;

        $rawData = DB::select($rawSql);
        $result = array();
        foreach ($rawData as $data) {
            $result[] = $data->cn;
        }

        return $result;
    }

    // protected function chartDataSnacks() :array
    // {
    //     $users = Snack::where('created_at','>', now()->sub(DateInterval::createFromDateString('30 days')))->orderBy('created_at', 'asc')->get();

    //     if (count($users) === 0) return array();

    //     $data = [];
    //     $count = 0;
    //     $interval = DateInterval::createFromDateString('1 day');

    //     $currentDate = $users->first()->created_at->add($interval);

    //     for ($i = 0; $i < count($users); $i++) {
    //         if ($users[$i]->created_at > $currentDate) {
    //             $currentDate = $currentDate->add($interval);
    //             $data[] = $count;
    //             $count = 0;
    //             $i--;
    //         } else {
    //             $count++;
    //         }
    //     }
    //     $data[] = $count;
    //     return $data;
    // }
}
