<?php

declare(strict_types=1);

namespace App\UseCase\ScheduleNewsletter;

use App\Doctrine\Entity\Newsletter;
use App\Doctrine\Repository\LiveRepository;
use App\Doctrine\Repository\NewsletterRepository;
use App\Doctrine\Repository\PostRepository;
use App\SendinBlue\SendinBlueItemInterface;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use SendinBlue\Client\Api\EmailCampaignsApi;
use SendinBlue\Client\Model\CreateEmailCampaign;
use SendinBlue\Client\Model\CreateEmailCampaignRecipients;
use SendinBlue\Client\Model\CreateEmailCampaignSender;
use Twig\Environment;

final class ScheduleNewsletter implements ScheduleNewsletterInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LiveRepository $liveRepository,
        private readonly PostRepository $postRepository,
        private readonly NewsletterRepository $newsletterRepository,
        private readonly EmailCampaignsApi $emailCampaignsApi,
        private readonly int $sendinBlueTemplateId,
        private readonly int $sendinBlueListId,
        private readonly string $appHost,
        private readonly Environment $twig
    ) {
    }

    public function schedule(): void
    {
        /** @var ?Newsletter $lastNewsletter */
        $lastNewsletter = $this->newsletterRepository->findOneBy([], ['scheduledAt' => 'DESC']);

        $publishedFrom = $lastNewsletter?->getScheduledAt() ?? new DateTimeImmutable('1 week ago');

        $newsletter = new Newsletter();
        $newsletter->setScheduledAt(new DateTimeImmutable('next sunday 18 hours'));

        foreach ($this->liveRepository->getLivesFrom($publishedFrom) as $item) {
            $newsletter->getLives()->add($item);
        }

        foreach ($this->postRepository->getPostsFrom($publishedFrom) as $item) {
            $newsletter->getPosts()->add($item);
        }

        if ($newsletter->getItems()->isEmpty()) {
            return;
        }

        $this->entityManager->persist($newsletter);
        $this->entityManager->flush();

        $response = $this->emailCampaignsApi->createEmailCampaign(
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

        $newsletter->setCampaignId($response->getId());
        $this->entityManager->flush();
    }
}
