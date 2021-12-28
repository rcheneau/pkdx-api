<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Entity\Pokemon;
use App\Entity\PokemonType;
use Symfony\Component\HttpFoundation\Request;

final class PokemonTypeTest extends AbstractApiTestCase
{
    private const ENDPOINT = '/api/pokemon_types';

    public function testGetCollectionPokemon()
    {
        $client = self::createClient();

        $client->request(Request::METHOD_GET, self::ENDPOINT);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(
            [
                '@context' => '/api/contexts/PokemonType',
                '@id'      => self::ENDPOINT,
                '@type'    => 'hydra:Collection',
            ]
        );
        $this->assertMatchesResourceCollectionJsonSchema(Pokemon::class);
    }

    public function testGetItemPokemon()
    {
        $client = self::createClient();

        $client->request(Request::METHOD_GET, self::ENDPOINT . '/1');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(
            [
                '@context' => '/api/contexts/PokemonType',
                '@id'      => self::ENDPOINT . '/1',
                '@type'    => 'PokemonType',
            ]
        );
        $this->assertMatchesResourceItemJsonSchema(PokemonType::class);
    }
}
