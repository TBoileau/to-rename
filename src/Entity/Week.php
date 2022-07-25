<?php

declare(strict_types=1);

namespace App\Entity;

use IntlDateFormatter;
use Stringable;

use function Symfony\Component\String\u;

final class Week implements Stringable
{
    public int $startDay;

    public int $endDay;

    public string $month;

    /**
     * @param array<int, Live> $lives
     */
    public function __construct(public array $lives)
    {
        usort($this->lives, function (Live $a, Live $b) {
            return $a->getStartedAt() <=> $b->getStartedAt();
        });

        $this->startDay = (int) $this->lives[0]->getStartedAt()->format('j');
        $this->endDay = (int) $this->lives[count($this->lives) - 1]->getStartedAt()->format('j');

        $this->month = IntlDateFormatter::formatObject(
            /* @phpstan-ignore-next-line */
            $this->lives[0]->getStartedAt(),
            'MMMM',
            'fr_FR'
        );
    }

    public function __toString(): string
    {
        return sprintf('DU %d AU %d %s', $this->startDay, $this->endDay, u($this->month)->upper());
    }
}
