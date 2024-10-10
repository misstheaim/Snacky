<?php

namespace App\Filament\Resources;

use App\Models\Snack;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;

class TableController
{
    public static function getSnackTable() :array
    {
        $table = array(
            TextColumn::make('name'),
            TextColumn::make('category.name'),
            TextColumn::make('description')
                ->words(10)
                ->wrap(),
            TextColumn::make('price')
                ->money('UZS'),
            TextColumn::make('link')
                ->limit(30)
                ->url(fn (Snack $record) => $record->link)
                ->openUrlInNewTab(),
            TextColumn::make('user.name')
        );

        if (Auth::user()->isDev()) {
            $table[] = TextColumn::make('status')
                ->state(function (Snack $record) {
                    return match($record->status) {
                        'APPROVED' => 'Approved',
                        'DISAPPROVED' => 'Disapproved',
                        'IN_PROCESS' => 'In process'
                    };
                })
                ->badge()
                ->color(fn (string $state) => match($state) {
                    'Approved' => 'success',
                    'Disapproved' => 'warning',
                    'In process' => 'gray'
                })
                ->sortable();
        }

        if (Auth::user()->isManager() || Auth::user()->isAdmin()) {
            $table[] = SelectColumn::make('status')
                ->options([
                    'APPROVED' => 'Approved',
                    'DISAPPROVED' => 'Disapproved',
                    'IN_PROCESS' => 'In process'
                ])
                ->sortable();
        }


        return $table;
    }
}