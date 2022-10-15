<?php

declare(strict_types=1);

namespace App\Doctrine\Entity;

use App\Doctrine\Repository\NewsletterRepository;
use App\SendinBlue\SendinBlueItemInterface;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;

#[Entity(repositoryClass: NewsletterRepository::class)]
class Newsletter
{
    #[Id]
    #[GeneratedValue]
    #[Column]
    private ?int $id = null;

    #[Column]
    private DateTimeImmutable $scheduledAt;

    /**
     * @var Collection<int, Post>
     */
    #[ManyToMany(targetEntity: Post::class)]
    #[JoinTable(name: 'newsletter_posts')]
    private Collection $posts;

    /**
     * @var Collection<int, Live>
     */
    #[ManyToMany(targetEntity: Live::class)]
    #[JoinTable(name: 'newsletter_lives')]
    private Collection $lives;

    #[Column(nullable: true)]
    private ?int $campaignId = null;

    private ?int $uniqueClick = null;

    private ?int $clickers = null;

    private ?int $complaints = null;

    private ?int $delivered = null;

    private ?int $sent = null;

    private ?int $softBounces = null;

    private ?int $hardBounces = null;

    private ?int $uniqueViews = null;

    private ?int $trackableViews = null;

    private ?int $unsubscriptions = null;

    private ?int $viewed = null;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->lives = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScheduledAt(): DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    public function setScheduledAt(DateTimeImmutable $scheduledAt): void
    {
        $this->scheduledAt = $scheduledAt;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    /**
     * @return Collection<int, Live>
     */
    public function getLives(): Collection
    {
        return $this->lives;
    }

    /**
     * @return Collection<int, SendinBlueItemInterface>
     */
    public function getItems(): Collection
    {
        /* @phpstan-ignore-next-line */
        return new ArrayCollection(array_merge($this->posts->toArray(), $this->lives->toArray()));
    }

    public function getCampaignId(): ?int
    {
        return $this->campaignId;
    }

    public function setCampaignId(?int $campaignId): void
    {
        $this->campaignId = $campaignId;
    }

    public function getUniqueClick(): ?int
    {
        return $this->uniqueClick;
    }

    public function setUniqueClick(?int $uniqueClick): void
    {
        $this->uniqueClick = $uniqueClick;
    }

    public function getClickers(): ?int
    {
        return $this->clickers;
    }

    public function setClickers(?int $clickers): void
    {
        $this->clickers = $clickers;
    }

    public function getComplaints(): ?int
    {
        return $this->complaints;
    }

    public function setComplaints(?int $complaints): void
    {
        $this->complaints = $complaints;
    }

    public function getDelivered(): ?int
    {
        return $this->delivered;
    }

    public function setDelivered(?int $delivered): void
    {
        $this->delivered = $delivered;
    }

    public function getSent(): ?int
    {
        return $this->sent;
    }

    public function setSent(?int $sent): void
    {
        $this->sent = $sent;
    }

    public function getSoftBounces(): ?int
    {
        return $this->softBounces;
    }

    public function setSoftBounces(?int $softBounces): void
    {
        $this->softBounces = $softBounces;
    }

    public function getHardBounces(): ?int
    {
        return $this->hardBounces;
    }

    public function setHardBounces(?int $hardBounces): void
    {
        $this->hardBounces = $hardBounces;
    }

    public function getUniqueViews(): ?int
    {
        return $this->uniqueViews;
    }

    public function setUniqueViews(?int $uniqueViews): void
    {
        $this->uniqueViews = $uniqueViews;
    }

    public function getTrackableViews(): ?int
    {
        return $this->trackableViews;
    }

    public function setTrackableViews(?int $trackableViews): void
    {
        $this->trackableViews = $trackableViews;
    }

    public function getUnsubscriptions(): ?int
    {
        return $this->unsubscriptions;
    }

    public function setUnsubscriptions(?int $unsubscriptions): void
    {
        $this->unsubscriptions = $unsubscriptions;
    }

    public function getViewed(): ?int
    {
        return $this->viewed;
    }

    public function setViewed(?int $viewed): void
    {
        $this->viewed = $viewed;
    }
}
