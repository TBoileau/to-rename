<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PodcastRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

#[Entity(repositoryClass: PodcastRepository::class)]
class Podcast extends Content
{
    /**
     * @var array<array-key, string>
     */
    #[Column(type: Types::JSON)]
    private array $guests;

    public static function getName(): string
    {
        return 'podcast';
    }

    /**
     * @return array<array-key, string>
     */
    public function getGuests(): array
    {
        return $this->guests;
    }

    /**
     * @param array<array-key, string> $guests
     */
    public function setGuests(array $guests): void
    {
        $this->guests = $guests;
    }
}
