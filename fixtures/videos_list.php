<?php

declare(strict_types=1);

use Google\Service\YouTube\SearchListResponse;
use Google\Service\YouTube\Thumbnail;
use Google\Service\YouTube\ThumbnailDetails;
use Google\Service\YouTube\Video;
use Google\Service\YouTube\VideoLocalization;
use Google\Service\YouTube\VideoSnippet;

$searchListResponse = new SearchListResponse();

return static function (string $id): Video {
    $video = new Video();

    $jsonData = json_decode(file_get_contents(__DIR__.'/videos_list.json'));

    $data = $jsonData->{$id};

    $video->setKind($data->kind);
    $video->setEtag($data->etag);
    $video->setId($data->id);

    $snippet = new VideoSnippet();

    $snippet->setPublishedAt($data->snippet->publishedAt);
    $snippet->setChannelId($data->snippet->channelId);
    $snippet->setTitle($data->snippet->title);
    $snippet->setDescription($data->snippet->description);

    if (isset($data->snippet->thumbnails)) {
        $videoThumbnailDetails = new ThumbnailDetails();

        if (isset($data->snippet->thumbnails->default)) {
            $videoThumbnail = new Thumbnail();

            $videoThumbnail->setUrl($data->snippet->thumbnails->default->url);
            $videoThumbnail->setWidth($data->snippet->thumbnails->default->width);
            $videoThumbnail->setHeight($data->snippet->thumbnails->default->height);

            $videoThumbnailDetails->setDefault($videoThumbnail);
        }

        if (isset($data->snippet->thumbnails->medium)) {
            $videoThumbnail = new Thumbnail();

            $videoThumbnail->setUrl($data->snippet->thumbnails->medium->url);
            $videoThumbnail->setWidth($data->snippet->thumbnails->medium->width);
            $videoThumbnail->setHeight($data->snippet->thumbnails->medium->height);

            $videoThumbnailDetails->setMedium($videoThumbnail);
        }

        if (isset($data->snippet->thumbnails->high)) {
            $videoThumbnail = new Thumbnail();

            $videoThumbnail->setUrl($data->snippet->thumbnails->high->url);
            $videoThumbnail->setWidth($data->snippet->thumbnails->high->width);
            $videoThumbnail->setHeight($data->snippet->thumbnails->high->height);

            $videoThumbnailDetails->setHigh($videoThumbnail);
        }

        if (isset($data->snippet->thumbnails->standard)) {
            $videoThumbnail = new Thumbnail();

            $videoThumbnail->setUrl($data->snippet->thumbnails->standard->url);
            $videoThumbnail->setWidth($data->snippet->thumbnails->standard->width);
            $videoThumbnail->setHeight($data->snippet->thumbnails->standard->height);

            $videoThumbnailDetails->setStandard($videoThumbnail);
        }

        if (isset($data->snippet->thumbnails->maxres)) {
            $videoThumbnail = new Thumbnail();

            $videoThumbnail->setUrl($data->snippet->thumbnails->maxres->url);
            $videoThumbnail->setWidth($data->snippet->thumbnails->maxres->width);
            $videoThumbnail->setHeight($data->snippet->thumbnails->maxres->height);

            $videoThumbnailDetails->setMaxres($videoThumbnail);
        }

        $snippet->setThumbnails($videoThumbnailDetails);
    }

    $snippet->setChannelTitle($data->snippet->channelTitle);
    $snippet->setTags($data->snippet->tags ?? []);

    if (isset($data->snippet->categoryId)) {
        $snippet->setCategoryId($data->snippet->categoryId);
    }

    if (isset($data->snippet->liveBroadcastContent)) {
        $snippet->setLiveBroadcastContent($data->snippet->liveBroadcastContent);
    }

    if (isset($data->snippet->defaultLanguage)) {
        $snippet->setDefaultLanguage($data->snippet->defaultLanguage);
    }

    if (isset($data->snippet->defaultAudioLanguage)) {
        $snippet->setDefaultAudioLanguage($data->snippet->defaultAudioLanguage);
    }

    if (isset($data->snippet->localized)) {
        $videoLocalized = new VideoLocalization();
        $videoLocalized->setTitle($data->snippet->localized->title);
        $videoLocalized->setDescription($data->snippet->localized->description);

        $snippet->setLocalized($videoLocalized);
    }

    $video->setSnippet($snippet);

    return $video;
};
