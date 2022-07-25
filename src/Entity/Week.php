<?php

declare(strict_types=1);

namespace App\Entity;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use IntlDateFormatter;
use Stringable;

use function Symfony\Component\String\u;

final class Week implements Stringable
{
    public DateTimeImmutable $start;

    public string $month;

    /**
     * @param array<int, Live> $lives
     */
    public function __construct(public array $lives)
    {
        $week = (int) $this->lives[0]->getStartedAt()->format('W');
        $year = (int) $this->lives[0]->getStartedAt()->format('Y');
        $this->start = (new DateTimeImmutable())->setISODate($year, $week);
        /* @phpstan-ignore-next-line */
        $this->month = IntlDateFormatter::formatObject($this->start, 'MMMM', 'fr_FR');

        $this->lives = array_combine(
            array_map(
                static fn (Live $live): int => intval($live->getStartedAt()->format('N')),
                $this->lives
            ),
            $this->lives
        );

        ksort($this->lives);
    }

    /**
     * @return iterable<Live|null>
     */
    public function getLives(): iterable
    {
        $end = $this->start->add(new DateInterval('P5D'));

        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($this->start, $interval, $end);

        foreach ($dateRange as $date) {
            yield $this->lives[intval($date->format('N'))] ?? null;
        }
    }

    public function __toString(): string
    {
        $start = (int) $this->start->format('j');

        return sprintf(
            'DU %d AU %d %s',
            $start,
            $start + 4,
            u($this->month)->upper()
        );
    }
}
