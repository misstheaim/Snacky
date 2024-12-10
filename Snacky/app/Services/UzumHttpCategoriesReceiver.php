<?php

namespace App\Services;

use App\Contracts\HttpCategoriesReceiver;
use App\Models\Category;
use App\Services\Helpers\HelperSortCategoryData;
use Illuminate\Support\Facades\Http;

class UzumHttpCategoriesReceiver implements HttpCategoriesReceiver
{
    public $categoriesName = [         //
        'Продукты питания',                 // Categories to filter
        'Oziq-ovqat mahsulotlari',          // {- Add all translations -}
        //'Хобби и творчество',
        //'Xobbi va ijod',
    ];

    public function receiveCategoriesData(string $lang): mixed
    {
        $response = Http::getUzumCategories()
            ->withOptions([
                'curl' => [                                 //
                    CURLOPT_SSL_ENABLE_ALPN => false,        // Just for local development
                ],                                           //
            ])
            ->withHeader('Accept-Language', $lang)
            ->get('/')->json();

        return $response;
    }

    public function addReceivedDataToDatabase(array $data): void
    {
        $index = 0;
        foreach ($data as $record) {
            if ($index == 0) {
                Category::upsert(
                    $record,
                    [],
                    [
                        'title_ru', 'uzum_category_id',
                    ]
                );
            } else {
                Category::upsert($record, ['uzum_category_id'], ['title_uz']);
            }
            $index++;
        }
    }

    public function makeWork(): void
    {
        $response = [];
        foreach (config('uzum.accept_language_header') as $key => $lang) {
            $response[$key] = $this->receiveCategoriesData($lang)['payload'];
        }

        $data = HelperSortCategoryData::getCategoriesByParent($response, $this->categoriesName);

        $this->addReceivedDataToDatabase($data);
    }
}
