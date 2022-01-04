<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use App\Repository\PokemonTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Locastic\ApiPlatformTranslationBundle\Model\AbstractTranslatable;
use Locastic\ApiPlatformTranslationBundle\Model\TranslationInterface;
use Symfony\Component\Serializer\Annotation\Groups;

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

    /**
     * @param int                                              $id
     * @param array<string, array{locale:string, name:string}> $translations
     */
    public function __construct(int $id, array $translations)
    {
        parent::__construct();
        $this->id           = $id;
        $this->translations = new ArrayCollection(
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
}
