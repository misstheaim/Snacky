<?php

namespace App\Contracts;

interface HttpProductReceiver
{
    public function receiveProductData(int $productId) :array;

    public function addReceivedDataToDatabase(array $data);

    public function makeWork(int $productId);
}