<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use App\Filament\Resources\Helpers\HelperFunctions;
use App\Filament\Resources\SubmissionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubmission extends CreateRecord
{
    protected static string $resource = SubmissionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

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
}
