<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PokemonTypeAffinityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PokemonTypeAffinityRepository::class)]
class PokemonTypeAffinity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: PokemonType::class, inversedBy: 'toTypeAffinities')]
    private PokemonType $fromType;

    #[ORM\ManyToOne(targetEntity: PokemonType::class, inversedBy: 'fromTypeAffinities')]
    private PokemonType $toType;

    #[ORM\Column(type: 'float')]
    private float $modifier;

    public function __construct(PokemonType $fromType, PokemonType $toType, float $modifier)
    {
        $this->fromType = $fromType;
        $this->toType   = $toType;
        $this->modifier = $modifier;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFromType(): PokemonType
    {
        return $this->fromType;
    }

    public function getToType(): PokemonType
    {
        return $this->toType;
    }

    public function getModifier(): float
    {
        return $this->modifier;
    }
}
