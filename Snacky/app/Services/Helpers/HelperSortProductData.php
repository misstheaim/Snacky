<?php

namespace App\Services\Helpers;

use App\Models\Category;

class HelperSortProductData
{
    public static function getSortedProduct($record) 
    {
        $result = array();
        $data = $record['payload']['data'];
        $result['uzum_product_id'] = $data['id'];
        $result['title_ru'] = $data['localizableTitle']['ru'];
        $result['title_uz'] = $data['localizableTitle']['uz'];
        $result['category_id'] = self::getCategoryIdIfItExistsOrNull($data['category']);
        $result['description_ru'] = strip_tags($data['description']);
        $result['high_image_link'] = $data['photos'][0]['photo']['800']['high'];
        $result['low_image_link'] = $data['photos'][0]['photo']['80']['low'];
        $result['price'] = $data['skuList'][0]['purchasePrice'];

        return $result;
    }

    private static function getCategoryIdIfItExistsOrNull(array $category) :int|null
    {
        $result = null;
        if (Category::where('uzum_category_id', $category['id'])->exists()) {
            $result =  $category['id'];
        } else if (!is_null($category['parent'])) {
            $result = self::getCategoryIdIfItExistsOrNull($category['parent']);
        }

        return $result;
    }
}