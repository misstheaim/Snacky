<?php

namespace App\Services;

use App\Contracts\HttpProductReceiver;
use App\Services\UzumHelpers\UzumTokenReceiver;
use Illuminate\Support\Facades\Http;

class UzumHttpGraphQLProductReceiver implements HttpProductReceiver
{
    public function receiveProductData($productId) :array
    {
        $token = UzumTokenReceiver::getToken();
        $response = Http::getUzumProductGraphQl()
            ->withOptions([
                'curl' => [                                 //
                    CURLOPT_SSL_ENABLE_ALPN => false        // Just for local development
                ]                                           //
            ])
            ->withToken($token)
            ->withHeader('Accept-Language', config('uzum.accept_language_header.ru'))
            ->post('/' , $this->getGraphQlQuery([
                "productId" => $productId,
                "linkTrans4" => "PRODUCT_240",
                "linkTrans5" => "PRODUCT_240",
                "linkTrans6" => "PRODUCT_240",
                "linkTrans7" => "PRODUCT_240"
            ]))
            ->json();

        dump($response);
        return $response;
        
    }

    public function addReceivedDataToDatabase($data)
    {
        
    }

    public function makeWork($productId)
    {

    }

    private function getGraphQlQuery(array $variables) :array
    {
        $query = <<< UZUM_QUERY
            query ProductPage(\$productId: Int!, \$linkTrans4: Transformation!, \$linkTrans6: Transformation!, \$linkTrans5: Transformation!, \$linkTrans7: Transformation!) {
                productPage(id: \$productId) {
                    product {
                    id
                    ordersQuantity
                    feedbackQuantity
                    feedbackPhotosCount
                    photo360 {
                        key
                        link(trans: PRODUCT_720) {
                        high
                        low
                        __typename
                        }
                        __typename
                    }
                    photos {
                        key
                        link(trans: PRODUCT_720) {
                        high
                        low
                        __typename
                        }
                        __typename
                    }
                    rating
                    video {
                        key
                        url
                        __typename
                    }
                    title
                    category {
                        id
                        parentList {
                        id
                        title
                        __typename
                        }
                        title
                        __typename
                    }
                    minFullPrice
                    minSellPrice
                    characteristics {
                        id
                        title
                        type
                        values {
                        id
                        photo {
                            key
                            link(trans: PRODUCT_240) {
                            high
                            low
                            __typename
                            }
                            __typename
                        }
                        title
                        value
                        characteristic {
                            id
                            title
                            type
                            values {
                            id
                            photo {
                                link(trans: \$linkTrans7) {
                                high
                                low
                                __typename
                                }
                                key
                                __typename
                            }
                            title
                            value
                            __typename
                            }
                            __typename
                        }
                        __typename
                        }
                        __typename
                    }
                    badges {
                        ... on BottomIconTextBadge {
                        backgroundColor
                        description
                        iconLink
                        id
                        link
                        text
                        textColor
                        __typename
                        }
                        ... on BottomTextBadge {
                        backgroundColor
                        description
                        id
                        link
                        text
                        textColor
                        __typename
                        }
                        ... on TopTextBadge {
                        backgroundColor
                        id
                        text
                        textColor
                        __typename
                        }
                        __typename
                    }
                    description
                    favorite
                    shop {
                        avatar {
                        low
                        __typename
                        }
                        feedbackQuantity
                        id
                        official
                        ordersQuantity
                        rating
                        seller {
                        accountId
                        legalRecords {
                            name
                            value
                            __typename
                        }
                        __typename
                        }
                        shortTitle
                        title
                        url
                        __typename
                    }
                    shortDescription
                    skuList {
                        id
                        availableAmount
                        photo {
                        key
                        link(trans: \$linkTrans4) {
                            low
                            __typename
                        }
                        __typename
                        }
                        fastDeliveryInfo {
                        title
                        badge {
                            text
                            color
                            __typename
                        }
                        __typename
                        }
                        photos {
                        key
                        link(trans: PRODUCT_720) {
                            high
                            low
                            __typename
                        }
                        __typename
                        }
                        paymentOptions {
                        paymentPerMonth
                        paymentInfo
                        text
                        type
                        id
                        active
                        __typename
                        }
                        skuTitle
                        sellPrice
                        discount {
                        discountPrice
                        discountAmount
                        priceText
                        modalHeader
                        modalText
                        __typename
                        }
                        properties {
                        description
                        filter {
                            description
                            id
                            measurementUnit
                            title
                            type
                            __typename
                        }
                        id
                        image
                        name
                        __typename
                        }
                        discountBadge {
                        backgroundColor
                        id
                        text
                        textColor
                        __typename
                        }
                        characteristicValues {
                        id
                        photo {
                            key
                            link(trans: \$linkTrans6) {
                            low
                            __typename
                            }
                            __typename
                        }
                        title
                        value
                        characteristic {
                            id
                            title
                            type
                            values {
                            id
                            photo {
                                key
                                link(trans: \$linkTrans5) {
                                high
                                low
                                __typename
                                }
                                __typename
                            }
                            title
                            value
                            __typename
                            }
                            __typename
                        }
                        __typename
                        }
                        fullPrice
                        vat {
                        vatRate
                        vatAmount
                        type
                        price
                        __typename
                        }
                        discountTimer {
                        endDate
                        text
                        textColor
                        __typename
                        }
                        __typename
                    }
                    attributes
                    __typename
                    }
                    fastDeliveryInfo {
                    title
                    badge {
                        text
                        color
                        __typename
                    }
                    __typename
                    }
                    returnsInfo {
                    link
                    __typename
                    }
                    originality {
                    isOriginal
                    __typename
                    }
                    warranty {
                    amount
                    __typename
                    }
                    actions {
                    location
                    type
                    ... on MotivationAction {
                        image {
                        low
                        high
                        __typename
                        }
                        location
                        text
                        type
                        __typename
                    }
                    ... on WishSaleAction {
                        dateEnd
                        location
                        pressed
                        pressedCount
                        type
                        __typename
                    }
                    __typename
                    }
                    installmentWidget {
                    title
                    titleColor
                    subtitle
                    subtitleColor
                    icon
                    link
                    lockedIcon
                    userStatus
                    __typename
                    }
                    __typename
                }
                }
        UZUM_QUERY;

        return [
            'operationName' => 'ProductPage',
            'query' => $query,
            'variables' => $variables
        ];
    }
}