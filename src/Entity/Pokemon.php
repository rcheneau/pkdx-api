<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use App\Enum\PokemonGrowthRateEnum;
use App\Repository\PokemonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Locastic\ApiPlatformTranslationBundle\Model\AbstractTranslatable;
use Locastic\ApiPlatformTranslationBundle\Model\TranslationInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @method PokemonTranslation getTranslation(?string $locale = null)
 */
#[ORM\Entity(repositoryClass: PokemonRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'path'   => '/pokemons',
            'method' => 'GET',
        ],
    ],
    itemOperations: [
        'get' => [
            'path'   => '/pokemons/{id}',
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

    #[ORM\Column(type: 'float')]
    #[Groups(['pokemon'])]
    private float $height;

    #[Groups(['pokemon'])]
    #[ORM\Column(type: 'float')]
    private float $weight;

    #[Groups(['pokemon'])]
    #[ORM\Column(type: 'integer')]
    private int $hp;

    #[Groups(['pokemon'])]
    #[ORM\Column(type: 'integer')]
    private int $attack;

    #[Groups(['pokemon'])]
    #[ORM\Column(type: 'integer')]
    private int $defense;

    #[Groups(['pokemon'])]
    #[ORM\Column(type: 'integer')]
    private int $spAttack;

    #[Groups(['pokemon'])]
    #[ORM\Column(type: 'integer')]
    private int $spDefense;

    #[Groups(['pokemon'])]
    #[ORM\Column(type: 'integer')]
    private int $speed;

    #[Groups(['pokemon'])]
    #[ORM\Column(type: 'integer')]
    private int $catchRate;

    #[Groups(['pokemon'])]
    #[ORM\Column(type: 'integer')]
    private int $baseExperience;

    #[Groups(['pokemon'])]
    #[ORM\Column(type: 'integer')]
    private int $baseFriendship;

    #[ORM\Column(type: 'pokemon_growth_rate')]
    private PokemonGrowthRateEnum $growthRate;

    #[Groups(['pokemon'])]
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $percentageMale;

    #[Groups(['pokemon'])]
    #[ORM\Column(type: 'integer')]
    private int $eggCycles;

    /**
     * @param int                                              $id
     * @param array<string, array{locale:string, name:string}> $translations
     * @param PokemonType                                      $type1
     * @param PokemonType|null                                 $type2
     * @param float                                            $height
     * @param float                                            $weight
     * @param int                                              $hp
     * @param int                                              $attack
     * @param int                                              $defense
     * @param int                                              $spAttack
     * @param int                                              $spDefense
     * @param int                                              $speed
     * @param int                                              $catchRate
     * @param int                                              $baseExperience
     * @param int                                              $baseFriendship
     * @param PokemonGrowthRateEnum                            $growthRate
     * @param float|null                                       $percentageMale
     * @param int                                              $eggCycles
     */
    public function __construct(int                   $id,
                                array                 $translations,
                                PokemonType           $type1,
                                ?PokemonType          $type2,
                                float                 $height,
                                float                 $weight,
                                int                   $hp,
                                int                   $attack,
                                int                   $defense,
                                int                   $spAttack,
                                int                   $spDefense,
                                int                   $speed,
                                int                   $catchRate,
                                int                   $baseExperience,
                                int                   $baseFriendship,
                                PokemonGrowthRateEnum $growthRate,
                                ?float                $percentageMale,
                                int                   $eggCycles,
    )
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

        $this->height         = $height;
        $this->weight         = $weight;
        $this->hp             = $hp;
        $this->attack         = $attack;
        $this->defense        = $defense;
        $this->spAttack       = $spAttack;
        $this->spDefense      = $spDefense;
        $this->speed          = $speed;
        $this->catchRate      = $catchRate;
        $this->baseExperience = $baseExperience;
        $this->baseFriendship = $baseFriendship;
        $this->growthRate     = $growthRate;
        $this->percentageMale = $percentageMale;
        $this->eggCycles      = $eggCycles;
    }

    /**
     * @return float|null
     */
    public function getPercentageMale(): ?float
    {
        return $this->percentageMale;
    }

    /**
     * @return int
     */
    public function getEggCycles(): int
    {
        return $this->eggCycles;
    }

    public function getHp(): int
    {
        return $this->hp;
    }

    public function getAttack(): int
    {
        return $this->attack;
    }

    public function getDefense(): int
    {
        return $this->defense;
    }

    public function getSpAttack(): int
    {
        return $this->spAttack;
    }

    public function getSpDefense(): int
    {
        return $this->spDefense;
    }

    public function getSpeed(): int
    {
        return $this->speed;
    }

    public function getCatchRate(): int
    {
        return $this->catchRate;
    }

    public function getBaseExperience(): int
    {
        return $this->baseExperience;
    }

    public function getBaseFriendship(): int
    {
        return $this->baseFriendship;
    }

    public function getGrowthRate(): PokemonGrowthRateEnum
    {
        return $this->growthRate;
    }

    #[Groups(['pokemon'])]
    #[SerializedName('growthRate')]
    public function getGrowthRateLabel(): string
    {
        return $this->growthRate->value;
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

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    #[Pure]
    protected function createTranslation(): TranslationInterface
    {
        return new PokemonTranslation($this, '', '');
    }
}
