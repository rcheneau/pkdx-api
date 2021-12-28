<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use App\Repository\PokemonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Locastic\ApiPlatformTranslationBundle\Model\AbstractTranslatable;
use Locastic\ApiPlatformTranslationBundle\Model\TranslationInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @method PokemonTranslation getTranslation(?string $locale = null)
 */
#[ORM\Entity(repositoryClass: PokemonRepository::class)]
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
        'groups' => ['pokemon'],
    ]
)]
#[ApiFilter(GroupFilter::class, arguments: [
    'parameterName'         => 'groups',
    'overrideDefaultGroups' => false,
    'whitelist'             => ['translations', 'type'],
])]
class Pokemon extends AbstractTranslatable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: true)]
    private int $id;

    #[Groups(['pokemon'])]
    /** @phpstan-ignore-next-line Virtual property */
    private string $name;

    /** @var ArrayCollection<string, PokemonTranslation> */
    #[ORM\OneToMany(mappedBy: 'translatable', targetEntity: PokemonTranslation::class, cascade: ['persist'], fetch: 'EAGER', orphanRemoval: true, indexBy: 'locale')]
    #[Groups(['translations'])]
    #[ApiProperty(fetchEager: true)]
    protected $translations;

    #[ORM\ManyToOne(targetEntity: PokemonType::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['type'])]
    private PokemonType $type1;

    #[ORM\ManyToOne(targetEntity: PokemonType::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['type'])]
    private ?PokemonType $type2;

    /**
     * @param int                                              $id
     * @param array<string, array{locale:string, name:string}> $translations
     * @param PokemonType                                      $type1
     * @param PokemonType|null                                 $type2
     */
    public function __construct(int $id, array $translations, PokemonType $type1, ?PokemonType $type2)
    {
        parent::__construct();
        $this->id           = $id;
        $this->translations = new ArrayCollection(
            array_map(
                fn($v) => new PokemonTranslation($this, $v['locale'], $v['name']),
                $translations
            )
        );
        $this->type1        = $type1;
        $this->type2        = $type2;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /** @noinspection PhpUnused */
    #[Groups(['pokemon:read'])]
    public function getPokedexNumber(): int
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

    public function getType1(): PokemonType
    {
        return $this->type1;
    }

    public function getType2(): ?PokemonType
    {
        return $this->type2;
    }

    #[Pure]
    protected function createTranslation(): TranslationInterface
    {
        return new PokemonTranslation($this, '', '');
    }
}
