<?php

namespace App\Services;

use App\Contracts\HttpProductReceiver;
use App\Models\Snack;
use App\Services\Helpers\HelperSortProductData;
use Illuminate\Support\Facades\Http;

class UzumHttpProductReceiver implements HttpProductReceiver
{
    public function receiveProductData($productId): mixed
    {
        $response = Http::getUzumProduct()
            ->withOptions([
                'curl' => [                                 //
                    CURLOPT_SSL_ENABLE_ALPN => false,        // Just for local development
                ],                                           //
            ])
            ->withHeader('Accept-Language', config('uzum.accept_language_header.ru'))
            ->get($productId);

        if ($response->failed() || is_null($response->json()['payload'])) {
            return [
                'failed' => true,
                'error' => $response->json()['error'],
            ];
        }

        return $response->json();
    }

    public function addReceivedDataToDatabase($data): void
    {
        Snack::updateOrCreate(['uzum_product_id' => $data['uzum_product_id']], $data);
    }

    public function makeWork($productId): void
    {
        $response = $this->receiveProductData($productId);
        if (isset($response['failed'])) {
            return;
        }
        $data = HelperSortProductData::getSortedProduct($response);

        $this->addReceivedDataToDatabase($data);
    }
}
