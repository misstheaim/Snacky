<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->action(function () {
                    $this->resetTable();
                }),
            Action::make('update the data')
                ->action(function () {
                    $exitcode = 0;
                    $message = null;
                    try {
                        Artisan::call('receive-categories');
                    } catch (Exception $e) {
                        $exitcode = 1;
                        $message = $e->getMessage();
                    }
                    match ($exitcode) {
                        0 => Notification::make()
                            ->title('Data successfully updated')
                            ->success()
                            ->send(),
                        1 => Notification::make()
                            ->title('Something went wrong')
                            ->warning()
                            ->send(),
                    };
                    $this->resetTable();
                }),
        ];
    }
}
