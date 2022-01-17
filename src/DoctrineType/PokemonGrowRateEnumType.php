<?php

declare(strict_types=1);

namespace App\DoctrineType;

use App\Enum\PokemonGrowthRateEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class PokemonGrowRateEnumType extends Type
{
    public const NAME = 'pokemon_growth_rate';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'VARCHAR(30)';
    }

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param PokemonGrowthRateEnum $value
     * @param AbstractPlatform      $platform
     *
     * @return string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value->value;
    }

    /**
     * @param string           $value
     * @param AbstractPlatform $platform
     *
     * @return PokemonGrowthRateEnum|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?PokemonGrowthRateEnum
    {
        return PokemonGrowthRateEnum::tryFrom($value);
    }
}
