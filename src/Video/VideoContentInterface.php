<?php

declare(strict_types=1);

namespace App\Video;

interface VideoContentInterface
{
    public function getVideoDescription(): string;

    public function getVideoTitle(): string;
}
