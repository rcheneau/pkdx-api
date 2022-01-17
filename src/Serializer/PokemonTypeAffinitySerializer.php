<?php

declare(strict_types=1);

namespace App\Serializer;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\DTO\PokemonTypeAffinityOutput;
use App\Entity\PokemonType;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

final class PokemonTypeAffinitySerializer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'POKEMON_TYPE_AFFINITY_NORMALIZER_ALREADY_CALLED';

    private IriConverterInterface $iriConverter;

    public function __construct(IriConverterInterface $iriConverter)
    {
        $this->iriConverter = $iriConverter;
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof PokemonTypeAffinityOutput;
    }

    /**
     * @param PokemonTypeAffinityOutput $object
     * @param string|null               $format
     * @param array                     $context
     *
     * @return array
     * @throws ExceptionInterface
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $context[self::ALREADY_CALLED] = true;

        $data = $this->normalizer->normalize($object, $format, $context);

        $data['type'] = $this->iriConverter->getIriFromItem($object->type);

        return $data;
    }
}
