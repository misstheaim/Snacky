<?php

namespace App\Contracts;

interface HttpCategoriesReceiver
{
    public function receiveCategoriesData(string $lang): mixed;

    /**
     * @param  array<string, mixed>  $data
     */
    public function addReceivedDataToDatabase(array $data): void;

    public function makeWork(): void;
}
