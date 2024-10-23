<?php

namespace App\Services;

use App\Contracts\HttpCategoriesReceiver;
use App\Models\Category;
use App\Services\Helpers\HelperSortCategoryData;
use App\Services\UzumHelpers\UzumTokenReceiver;
use Illuminate\Support\Facades\Http;

class UzumHttpGraphQlCategoriesReceiver implements HttpCategoriesReceiver
{
    public $categoriesId = array(         
        1821,
        //
    );


    public function receiveCategoriesData(string $lang) :array
    {
        $token = UzumTokenReceiver::getToken();

        $response = Http::getUzumCategoriesGraphQl()
            ->withOptions([
                'curl' => [                                 //
                    CURLOPT_SSL_ENABLE_ALPN => false        // Just for local development
                ]                                           //
            ])
            ->withToken($token)
            ->withHeader('Accept-Language', config('uzum.accept_language_header.ru'))
            ->post('/' , $this->getGraphQlQuery())
            ->json();

        return $response;
    }

    public function addReceivedDataToDatabase(array $data)
    {
        dump($data);
        //Category::updateOrCreate(['uzum_category_id' => $data['uzum_category_id']], $data);
    }

    public function makeWork()
    {
        $response = $this->receiveCategoriesData('ru');

        $data = HelperSortCategoryData::getCategoriesGraphQl($response, $this->categoriesId);
        
        $this->addReceivedDataToDatabase($data);
    }


    private function getGraphQlQuery() :array
    {
        $query = <<< UZUM_QUERY
        query getMakeSearch(\$queryInput: MakeSearchQueryInput!) {
        makeSearch(query: \$queryInput) {
            id
            queryId
            queryText
            category {
            ...CategoryShortFragment
            __typename
            }
            categoryTree {
            category {
                ...CategoryFragment
                __typename
            }
            total
            __typename
            }
            items {
            adMarker {
                marker
                __typename
            }
            catalogCard {
                __typename
                ...SkuGroupCardFragment
            }
            bidId
            __typename
            }
            facets {
            ...FacetFragment
            __typename
            }
            fastFacets {
            ...FacetFragment
            __typename
            }
            total
            mayHaveAdultContent
            categoryFullMatch
            offerCategory {
            title
            id
            __typename
            }
            correctedQueryText
            categoryWasPredicted
            fastCategories {
            category {
                ...FastCategoryFragment
                __typename
            }
            total
            __typename
            }
            permanentLinkSeo {
            id
            seoHeader
            seoMetaTag
            seoTitle
            __typename
            }
            token
            __typename
        }
        }

        fragment FacetFragment on Facet {
        filter {
            id
            title
            type
            measurementUnit
            description
            __typename
        }
        buckets {
            filterValue {
            id
            description
            image
            name
            __typename
            }
            total
            __typename
        }
        range {
            min
            max
            __typename
        }
        __typename
        }

        fragment CategoryFragment on Category {
        id
        icon
        parent {
            id
            __typename
        }
        seo {
            header
            metaTag
            __typename
        }
        parentList {
            id
            title
            __typename
        }
        title
        title_ru
        title_uz
        adult
        __typename
        }

        fragment CategoryShortFragment on Category {
        id
        parent {
            id
            title
            title_ru
            title_uz
            __typename
        }
        title
        title_ru
        title_uz
        __typename
        }

        fragment FastCategoryFragment on Category {
        id
        parent {
            id
            title
            __typename
        }
        title
        seo {
            header
            metaTag
            __typename
        }
        __typename
        }

        fragment SkuGroupCardFragment on SkuGroupCard {
        ...DefaultCardFragment
        photos {
            key
            link(trans: PRODUCT_540) {
            high
            low
            __typename
            }
            previewLink: link(trans: PRODUCT_240) {
            high
            low
            __typename
            }
            __typename
        }
        badges {
            ... on BottomTextBadge {
            backgroundColor
            description
            id
            link
            text
            textColor
            __typename
            }
            ... on UzumInstallmentTitleBadge {
            backgroundColor
            text
            id
            textColor
            __typename
            }
            __typename
        }
        characteristicValues {
            id
            value
            title
            characteristic {
            values {
                id
                title
                value
                __typename
            }
            title
            id
            __typename
            }
            __typename
        }
        __typename
        }

        fragment DefaultCardFragment on CatalogCard {
        adult
        favorite
        feedbackQuantity
        id
        minFullPrice
        minSellPrice
        offer {
            due
            icon
            text
            textColor
            __typename
        }
        badges {
            backgroundColor
            text
            textColor
            __typename
        }
        discountInfo {
            text
            textColor
            backgroundColor
            __typename
        }
        ordersQuantity
        productId
        rating
        title
        __typename
        }
        UZUM_QUERY;

        return [
            "operationName" => "getMakeSearch",
            "query" => $query,
            "variables" => [
                "queryInput" => [
                    "categoryId" => "1821",
                    "showAdultContent" => "TRUE",
                    "filters" => [],
                    "sort" => "BY_RELEVANCE_DESC",
                    "pagination" => [
                        "offset" => 0,
                        "limit" => 0
                    ],
                    "correctQuery" => false,
                    "getFastCategories" => false,
                    "getPromotionItems" => false,
                    "getFastFacets" => false,
                    "fastFacetsLimit" => 0
                ]
            ]
        ];
    }
}


