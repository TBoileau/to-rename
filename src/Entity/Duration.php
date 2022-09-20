<?php

declare(strict_types=1);

namespace App\Entity;

use DateInterval;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

#[Embeddable]
class Duration
{
    #[GreaterThanOrEqual(0)]
    #[LessThanOrEqual(23)]
    #[Column(type: Types::INTEGER)]
    private int $hours;

    #[GreaterThanOrEqual(0)]
    #[LessThanOrEqual(59)]
    #[Column(type: Types::INTEGER)]
    private int $minutes;

    #[GreaterThanOrEqual(0)]
    #[LessThanOrEqual(59)]
    #[Column(type: Types::INTEGER)]
    private int $seconds;

    public function getHours(): int
    {
        return $this->hours;
    }

    public function setHours(int $hours): void
    {
        $this->hours = $hours;
    }

    public function getMinutes(): int
    {
        return $this->minutes;
    }

    public function setMinutes(int $minutes): void
    {
        $this->minutes = $minutes;
    }

    public function getSeconds(): int
    {
        return $this->seconds;
    }

    public function setSeconds(int $seconds): void
    {
        $this->seconds = $seconds;
    }

    public function addTo(DateTimeImmutable $date): DateTimeImmutable
    {
        return $date->add(
            new DateInterval(
                sprintf(
                    'PT%dH%dM%dS',
                    $this->hours,
                    $this->minutes,
                    $this->seconds
                )
            )
        );
    }
}
