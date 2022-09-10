<?php

declare(strict_types=1);

namespace App\Google\Youtube;

use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Google\Service\YouTube\Thumbnail;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class VideoSynchronizer implements VideoSynchronizerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private VideoHandlerInterface $videoHandler,
    ) {
    }

    public function synchronize(): void
    {
        $videos = $this->videoHandler->list();

        $videoRepository = $this->entityManager->getRepository(Video::class);

        foreach ($videos as $youtubeVideo) {
            $video = $videoRepository->findOneBy(['youtubeId' => $youtubeVideo->getId()]);

            if (null === $video) {
                $video = new Video();
                $video->setYoutubeId($youtubeVideo->getId());
                $this->entityManager->persist($video);
            }

            $video->setTitle($youtubeVideo->getSnippet()->getTitle());
            $video->setDescription($youtubeVideo->getSnippet()->getDescription());
            $video->setTags($youtubeVideo->getSnippet()->getTags());

            /** @var array<string, string> $thumbnails */
            $thumbnails = [];

            $propertyAccessor = PropertyAccess::createPropertyAccessor();

            foreach (['default', 'medium', 'high', 'standard', 'maxres'] as $type) {
                /** @var Thumbnail $thumbnail */
                $thumbnail = $propertyAccessor->getValue($youtubeVideo->getSnippet()->getThumbnails(), $type);
                $thumbnails[$type] = $thumbnail->getUrl();
            }

            $video->setThumbnails($thumbnails);
        }

        $this->entityManager->flush();
    }
}
