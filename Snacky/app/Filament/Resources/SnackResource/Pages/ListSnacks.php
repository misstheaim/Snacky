<?php

namespace App\Filament\Resources\SnackResource\Pages;

use App\Filament\Resources\SnackResource;
use App\Filament\Resources\Templates\HelperFunctions;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListSnacks extends ListRecords
{
    protected static string $resource = SnackResource::class;

    protected function getHeaderActions(): array
    {
        return HelperFunctions::isUser(Auth::user())->isDev() ? [
            Action::make('refresh')
                ->action(function () {
                    $this->resetTable();
                })
        ] : [
            Actions\CreateAction::make(),
            Action::make('refresh')
                ->action(function () {
                    $this->resetTable();
                })
        ];
    }
}
