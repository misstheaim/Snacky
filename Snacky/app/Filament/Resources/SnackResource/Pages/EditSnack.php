<?php

namespace App\Filament\Resources\SnackResource\Pages;

use App\Filament\Resources\Helpers\HelperFunctions;
use App\Filament\Resources\SnackResource;
use App\Models\Receipt;
use App\Models\Snack;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSnack extends EditRecord
{
    protected static string $resource = SnackResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uzum_product_id'] =  HelperFunctions::$buffer['uzum_product_id'];
        $data['title_ru'] =  HelperFunctions::$buffer['title_ru'];
        $data['title_uz'] =  HelperFunctions::$buffer['title_uz'];
        $data['price'] =  HelperFunctions::$buffer['price'];
        $data['category_id'] =  HelperFunctions::$buffer['category_id'];
        $data['description_ru'] =  HelperFunctions::$buffer['description_ru'];
        $data['high_image_link'] =  HelperFunctions::$buffer['high_image_link'];
        $data['low_image_link'] =  HelperFunctions::$buffer['low_image_link'];
    
        return $data;
    }

    protected function getHeaderActions(): array
    {
        $receipts_exists = Snack::where('id', $this->record->id)->withExists('receipts')->first()->receipts_exists;
        return [
            Actions\DeleteAction::make()
                ->modalDescription(fn () => $receipts_exists ? 'This snack attached to the receipt, are you sure you want to delete it? All attached receipts will be recalculated' : 'Are you sure you would like to do this?')
                ->color(fn () => $receipts_exists ? 'danger' : 'warning')
                ->modalHeading(fn () => $receipts_exists ? 'Warning! Deleting attached Snack!' : 'Delete Snack')
                ->before(function () use ($receipts_exists) {
                    if ($receipts_exists) {
                        $receipts = Receipt::all();
                        foreach ($receipts as $receipt) {
                            $receipt->snacks()->detach($this->record->id);
                        }
                    }
                }),
        ];
    }

    public function getRelationManagers() :array
    {
        return [
            
        ];
    }
}
