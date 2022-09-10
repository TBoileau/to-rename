<?php

declare(strict_types=1);

namespace App\Google\Youtube;

interface VideoSynchronizerInterface
{
    public function synchronize(): void;
}
