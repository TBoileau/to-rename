<?php

declare(strict_types=1);

use Google\Service\YouTube\PageInfo;
use Google\Service\YouTube\ResourceId;
use Google\Service\YouTube\SearchListResponse;
use Google\Service\YouTube\SearchResult;

$searchListResponse = new SearchListResponse();

return static function (?string $pageToken): SearchListResponse {
    $searchListResponse = new SearchListResponse();

    $jsonData = json_decode(file_get_contents(__DIR__.'/search_list.json'));

    $data = $jsonData->{$pageToken ?? ''};

    $searchListResponse->setKind($data->kind);
    $searchListResponse->setEtag($data->etag);

    if (isset($data->nextPageToken)) {
        $searchListResponse->setNextPageToken($data->nextPageToken);
    }

    $searchListResponse->setRegionCode($data->regionCode);

    $pageInfo = new PageInfo();
    $pageInfo->setTotalResults($data->pageInfo->totalResults);
    $pageInfo->setResultsPerPage($data->pageInfo->resultsPerPage);

    $searchListResponse->setPageInfo($pageInfo);

    /** @var array<array-key, SearchResult> $items */
    $items = [];

    foreach ($data->items as $item) {
        $searchResult = new SearchResult();
        $searchResult->setKind($item->kind);
        $searchResult->setEtag($item->etag);

        $id = new ResourceId();
        $id->setKind($item->id->kind);
        $id->setVideoId($item->id->videoId);

        $searchResult->setId($id);

        $items[] = $searchResult;
    }

    $searchListResponse->setItems($items);

    return $searchListResponse;
};
