<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Validator\Constraints\NotNull;

#[Entity]
class ChallengeRule
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Challenge::class, inversedBy: 'rules')]
    private ?Challenge $challenge = null;

    #[NotNull]
    #[ManyToOne(targetEntity: Rule::class)]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Rule $rule;

    #[Column(type: Types::INTEGER)]
    private int $hits = 0;

    public static function createFromRule(Rule $rule): self
    {
        $challengeRule = new self();
        $challengeRule->rule = $rule;

        return $challengeRule;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChallenge(): ?Challenge
    {
        return $this->challenge;
    }

    public function setChallenge(?Challenge $challenge): void
    {
        $this->challenge = $challenge;
    }

    public function getRule(): Rule
    {
        return $this->rule;
    }

    public function setRule(Rule $rule): void
    {
        $this->rule = $rule;
    }

    public function getHits(): int
    {
        return $this->hits;
    }

    public function setHits(int $hits): void
    {
        $this->hits = $hits;
    }

    public function getTotal(): int
    {
        return $this->hits * $this->rule->getPoints();
    }
}
