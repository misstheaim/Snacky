<?php

namespace App\Contracts;

interface HttpProductReceiver
{
    public function receiveProductData(int $productId): mixed;

    /**
     * @param  array<string, mixed>  $data
     */
    public function addReceivedDataToDatabase(array $data): void;

    public function makeWork(int $productId): void;
}
