<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use App\Filament\Resources\Helpers\HelperFunctions;
use App\Filament\Resources\SubmissionResource;
use App\Models\Snack;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

/** @property \App\Models\Snack $record */
class EditSubmission extends EditRecord
{
    protected static string $resource = SubmissionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uzum_product_id'] = HelperFunctions::$buffer['uzum_product_id'];
        $data['title_ru'] = HelperFunctions::$buffer['title_ru'];
        $data['title_uz'] = HelperFunctions::$buffer['title_uz'];
        $data['price'] = HelperFunctions::$buffer['price'];
        $data['category_id'] = HelperFunctions::$buffer['category_id'];
        $data['description_ru'] = HelperFunctions::$buffer['description_ru'];
        $data['high_image_link'] = HelperFunctions::$buffer['high_image_link'];
        $data['low_image_link'] = HelperFunctions::$buffer['low_image_link'];

        return $data;
    }

    protected function getHeaderActions(): array
    {
        $receipts_exists = Snack::where('id', $this->record->id)->withExists('receipts')->first()->receipts_exists;

        return [
            Actions\DeleteAction::make()
                ->modalDescription(fn () => $receipts_exists ? 'This snack attached to the receipt, you cannot delete snacks attached to the receipt' : 'Are you sure you would like to do this?')
                ->color(fn () => $receipts_exists ? 'danger' : 'warning')
                ->modalHeading(fn () => $receipts_exists ? 'Warning! Deleting attached Snack!' : 'Delete Snack')
                ->before(function (Actions\DeleteAction $action) use ($receipts_exists) {
                    if ($receipts_exists) {
                        Notification::make()
                            ->danger()
                            ->title('You cannot delete snack because it exists in receipt!')
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }
}
