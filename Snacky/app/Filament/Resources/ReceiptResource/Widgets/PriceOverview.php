<?php

namespace App\Filament\Resources\ReceiptResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class PriceOverview extends BaseWidget
{
    public ?Model $record = null;

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $total_price = 0;
        foreach ($this->record->snacks as $snack) {
            $total_price += $snack->price * $snack->pivot->item_count;
        }
        if($this->record->total_price !== $total_price) {
            $this->record->total_price = $total_price;
            $this->record->save();
        }
        return [
            Stat::make('Total price', $this->record->total_price)
                ->extraAttributes(['style' => 'width: 100%'])
        ];
    }
}