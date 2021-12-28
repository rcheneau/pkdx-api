<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Entity\Pokemon;
use Symfony\Component\HttpFoundation\Request;

final class PokemonTest extends AbstractApiTestCase
{
    private const ENDPOINT = '/api/pokemons';

    public function testGetCollectionPokemon()
    {
        $client = self::createClient();

        $client->request(Request::METHOD_GET, self::ENDPOINT);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(
            [
                '@context' => '/api/contexts/Pokemon',
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
                '@context' => '/api/contexts/Pokemon',
                '@id'      => self::ENDPOINT . '/1',
                '@type'    => 'Pokemon',
            ]
        );
        $this->assertMatchesResourceItemJsonSchema(Pokemon::class);
    }
}
