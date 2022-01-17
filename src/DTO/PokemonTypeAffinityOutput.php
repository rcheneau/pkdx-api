<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\PokemonType;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
final class PokemonTypeAffinityOutput
{
    public function __construct(
        // Will be serialized through its own serializer to avoid infinite recursion
        public PokemonType $type,

        #[Groups(['type'])]
        public float $modifier,
    )
    {}
}
