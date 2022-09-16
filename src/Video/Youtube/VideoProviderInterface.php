<?php

declare(strict_types=1);

namespace App\Video\Youtube;

use Google\Service\YouTube\Video;

interface VideoProviderInterface
{
    public function findOneById(string $id): Video;

    /**
     * @return iterable<array-key, Video>
     */
    public function findAll(): iterable;
}
