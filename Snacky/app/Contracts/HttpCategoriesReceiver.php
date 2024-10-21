<?php

namespace App\Contracts;

interface HttpCategoriesReceiver
{
    public function receiveCategoriesData(string $lang) :array;

    public function addReceivedDataToDatabase(array $data);

    public function makeWork();
}