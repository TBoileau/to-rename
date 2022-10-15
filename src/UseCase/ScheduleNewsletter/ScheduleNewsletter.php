<?php

declare(strict_types=1);

namespace App\UseCase\ScheduleNewsletter;

use App\Doctrine\Entity\Newsletter;
use App\SendinBlue\SendinBlueItemInterface;
use DateTime;
use SendinBlue\Client\Api\EmailCampaignsApi;
use SendinBlue\Client\Model\CreateEmailCampaign;
use SendinBlue\Client\Model\CreateEmailCampaignRecipients;
use SendinBlue\Client\Model\CreateEmailCampaignSender;
use Twig\Environment;

final class ScheduleNewsletter implements ScheduleNewsletterInterface
{
    public function __construct(
        private readonly EmailCampaignsApi $emailCampaignsApi,
        private readonly int $sendinBlueTemplateId,
        private readonly int $sendinBlueListId,
        private readonly string $appHost,
        private readonly Environment $twig
    ) {
    }

    public function send(Newsletter $newsletter): void
    {
        $this->emailCampaignsApi->createEmailCampaign(
            (new CreateEmailCampaign())
                /* @phpstan-ignore-next-line */
                ->setScheduledAt(DateTime::createFromImmutable($newsletter->getScheduledAt()))
                ->setName(sprintf('Newsletter du %s', $newsletter->getScheduledAt()->format('d/m/Y')))
                ->setTemplateId($this->sendinBlueTemplateId)
                ->setSubject(sprintf('Newsletter du %s', $newsletter->getScheduledAt()->format('d/m/Y')))
                ->setRecipients((new CreateEmailCampaignRecipients())->setListIds([$this->sendinBlueListId]))
                ->setSender(
                    (new CreateEmailCampaignSender())
                        ->setName('toham.dev')
                        ->setEmail('newsletter@toham.dev')
                )
                ->setParams((object) [
                    'date' => $newsletter->getScheduledAt()->format('d/m/Y'),
                    'items' => $newsletter->getItems()
                        ->map(fn (SendinBlueItemInterface $item) => [
                            'title' => $item->getItemTitle(),
                            'url' => sprintf('%s%s', $this->appHost, $item->getItemUrl()),
                            'content' => $this->twig->createTemplate($item->getItemDescription())->render(['live' => $item]),
                            'image' => sprintf('%s/uploads/%s', $this->appHost, $item->getItemImage()),
                        ])
                        ->toArray(),
                ])
        );
    }
}
