<?php

namespace App\Filament\Resources\SnackResource\Pages;

use App\Filament\Resources\SnackResource;
use App\Filament\Resources\Templates\SnackTemplates;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSnack extends CreateRecord
{
    protected static string $resource = SnackResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uzum_product_id'] =  SnackTemplates::$buffer['uzum_product_id'];
        $data['title_ru'] =  SnackTemplates::$buffer['title_ru'];
        $data['title_uz'] =  SnackTemplates::$buffer['title_uz'];
        $data['price'] =  SnackTemplates::$buffer['price'];
        $data['category_id'] =  SnackTemplates::$buffer['category_id'];
        $data['description_ru'] =  SnackTemplates::$buffer['description_ru'];
        $data['high_image_link'] =  SnackTemplates::$buffer['high_image_link'];
        $data['low_image_link'] =  SnackTemplates::$buffer['low_image_link'];
    
        return $data;
    }
}
