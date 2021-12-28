<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Locastic\ApiPlatformTranslationBundle\Model\AbstractTranslation;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class PokemonTranslation extends AbstractTranslation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: Pokemon::class, inversedBy: 'translations')]
    protected $translatable;

    #[ORM\Column(type: 'string')]
    protected $locale;

    #[ORM\Column(type: 'string')]
    #[Groups(['translations'])]
    private string $name;

    public function __construct(Pokemon $pokemon, string $locale, string $name)
    {
        $this->translatable = $pokemon;
        $this->locale       = $locale;
        $this->name         = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
