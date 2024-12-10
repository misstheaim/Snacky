<?php

namespace App\Filament\Resources\ReceiptResource\Pages;

use App\Filament\Resources\ReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReceipt extends EditRecord
{
    protected static string $resource = ReceiptResource::class;

    protected int|string|array $columnSpan = 1;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFooterWidgets(): array
    {
        return [
            ReceiptResource\Widgets\PriceOverview::class,
            ReceiptResource\Widgets\TableWidget::class,
        ];
    }

    public function getRelationManagers(): array
    {
        return [

        ];
    }
}
