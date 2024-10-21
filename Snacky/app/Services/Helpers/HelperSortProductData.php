<?php

namespace App\Services\Helpers;


class HelperSortProductData
{
    public static function getSortedProduct($record) 
    {
        $result = array();
        $data = $record['payload']['data'];
        $result['uzum_product_id'] = $data['id'];
        $result['title_ru'] = $data['localizableTitle']['ru'];
        $result['title_uz'] = $data['localizableTitle']['uz'];
        $result['category_id'] = $data['category']['id'];
        $result['description_ru'] = strip_tags($data['description']);
        $result['high_image_link'] = $data['photos'][0]['photo']['800']['high'];
        $result['low_image_link'] = $data['photos'][0]['photo']['80']['low'];
        $result['price'] = $data['skuList'][0]['purchasePrice'];
        $result['link'] = 'dfhgfgkvhgfkhgcgfxdhfxcfjxghdfx';

        return $result;
    }
}