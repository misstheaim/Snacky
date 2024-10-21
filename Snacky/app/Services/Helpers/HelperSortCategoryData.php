<?php

namespace App\Services\Helpers;


class HelperSortCategoryData
{
    public static function getCategoriesByParent(array $array, array $parentNames) :array
    {
        $result = array();
        foreach ($array as $lang => $data) {
            $record = array();
            $filtered_data = array_filter($data, fn ($v) => in_array($v['title'], $parentNames) );
            $index = 0;
            $parent_id = -1;
            foreach($filtered_data as $data_per_category) {
                self::recursion($data_per_category, $record, $parent_id, $lang, $index);
            }
            $result[] = $record;
        }
        return $result;
    }

    private static function recursion($array, &$result, $parent_id, $lang, &$index) {
        $result[$index]['parent_id'] = (int)$parent_id;
        $result[$index]['uzum_category_id'] = $array['id'];
        match ($lang) {
            'ru' => $result[$index]['title_ru'] = $array['title'],
            'uz' => $result[$index]['title_uz'] = $array['title'],
        };
        if (count($array['children']) > 0) {
            foreach($array['children'] as $child) {
                $index++;
                $parent_id = $array['id'];
                self::recursion($child, $result, $parent_id, $lang, $index);
            }
        }
        return;
    }
}