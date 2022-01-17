<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use App\DTO\PokemonTypeAffinityOutput;
use App\Repository\PokemonTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Locastic\ApiPlatformTranslationBundle\Model\AbstractTranslatable;
use Locastic\ApiPlatformTranslationBundle\Model\TranslationInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @method PokemonTranslation getTranslation(?string $locale = null)
 */
#[ORM\Entity(repositoryClass: PokemonTypeRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'method' => 'GET',
        ],
    ],
    itemOperations: [
        'get' => [
            'method' => 'GET',
        ],
    ],
    normalizationContext: [
        'groups' => ['type'],
    ]
)]
#[ApiFilter(GroupFilter::class, arguments: [
    'parameterName'         => 'groups',
    'overrideDefaultGroups' => false,
    'whitelist'             => ['translations'],
])]
class PokemonType extends AbstractTranslatable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: true)]
    private int $id;

    #[Groups(['type'])]
    /** @phpstan-ignore-next-line Virtual property */
    private string $name;

    /** @var ArrayCollection<string, PokemonTypeTranslation> */
    #[ORM\OneToMany(mappedBy: 'translatable', targetEntity: PokemonTypeTranslation::class, cascade: ['persist'], fetch: 'EAGER', orphanRemoval: true, indexBy: 'locale')]
    #[Groups(['translations'])]
    #[ApiProperty(fetchEager: true)]
    protected $translations;

    /** @var Collection<int, PokemonTypeAffinity> */
    #[ORM\OneToMany(mappedBy: 'toType', targetEntity: PokemonTypeAffinity::class)]
    private Collection $fromTypeAffinities;

    /** @var Collection<int, PokemonTypeAffinity> */
    #[ORM\OneToMany(mappedBy: 'fromType', targetEntity: PokemonTypeAffinity::class)]
    private Collection $toTypeAffinities;

    /**
     * @param int                                              $id
     * @param array<string, array{locale:string, name:string}> $translations
     */
    public function __construct(int $id, array $translations)
    {
        parent::__construct();
        $this->id                 = $id;
        $this->fromTypeAffinities = new ArrayCollection();
        $this->toTypeAffinities   = new ArrayCollection();
        $this->translations       = new ArrayCollection(
            array_map(
                fn($v) => new PokemonTypeTranslation($this, $v['locale'], $v['name']),
                $translations
            )
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->getTranslation()->getName();
    }

    public function setName(string $name): self
    {
        $this->getTranslation()->setName($name);

        return $this;
    }

    #[Pure]
    protected function createTranslation(): TranslationInterface
    {
        return new PokemonTypeTranslation($this, '', '');
    }

    /**
     * @return array<int, PokemonTypeAffinityOutput>
     */
    #[Groups(['type'])]
    #[SerializedName('fromTypeAffinities')]
    public function getFromTypeAffinitiesValues(): array
    {
        return $this->fromTypeAffinities
            ->map(fn(PokemonTypeAffinity $a) => new PokemonTypeAffinityOutput($a->getFromType(), $a->getModifier()))
            ->toArray();
    }

    /**
     * @return array<int, PokemonTypeAffinityOutput>
     */
    #[Groups(['type'])]
    #[SerializedName('toTypeAffinities')]
    public function getToTypeAffinitiesValues(): array
    {
        return $this->toTypeAffinities
            ->map(fn(PokemonTypeAffinity $a) => new PokemonTypeAffinityOutput($a->getToType(), $a->getModifier()))
            ->toArray();
    }
}
