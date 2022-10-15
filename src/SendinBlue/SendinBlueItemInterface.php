<?php

declare(strict_types=1);

namespace App\SendinBlue;

interface SendinBlueItemInterface
{
    public function getItemTitle(): string;

    public function getItemDescription(): string;

    public function getItemImage(): string;

    public function getItemUrl(): string;
}
