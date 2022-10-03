<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Live;
use App\Repository\LiveRepository;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Domain\ValueObject\Uri;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/calendar.ics', name: 'calendar')]
final class CalendarController extends AbstractController
{
    public function __invoke(LiveRepository $liveRepository): Response
    {
        /** @var array<array-key, Live> $lives */
        $lives = $liveRepository->findAll();

        $calendar = new Calendar(
            array_map(
                static fn (Live $live): Event => (new Event())
                    ->setOccurrence(
                        new TimeSpan(
                            new DateTime($live->getLivedAt(), true),
                            new DateTime($live->getEndedAt(), true)
                        )
                    )
                    ->setDescription('')
                    ->setUrl(new Uri('https://twitch.tv/toham'))
                    ->setSummary(''),
                $lives
            )
        );

        $calendar = (new CalendarFactory())->createCalendar($calendar);

        return new Response((string) $calendar, Response::HTTP_OK, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="cal.ics"',
        ]);
    }
}
