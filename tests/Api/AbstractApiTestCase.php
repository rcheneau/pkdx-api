<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractApiTestCase extends ApiTestCase
{
    protected static function createClient(array $kernelOptions = [], array $defaultOptions = []): Client
    {
        return parent::createClient(
            $kernelOptions,
            array_merge(['base_uri' => 'https://pkdx-api.com'], $defaultOptions)
        );
    }

    /**
     * Get repository for given entity class.
     *
     * @param string $entityClass
     *
     * @return ServiceEntityRepository
     */
    protected static function getRepository(string $entityClass): ServiceEntityRepository
    {
        return self::getContainer()->get('doctrine')->getManager()->getRepository($entityClass);
    }

    /**
     * @return EntityManagerInterface
     */
    protected static function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get('doctrine')->getManager();
    }
}
