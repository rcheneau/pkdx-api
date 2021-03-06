<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PokemonType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PokemonType>
 *
 * @method PokemonType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PokemonType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PokemonType[]    findAll()
 * @method PokemonType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PokemonTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PokemonType::class);
    }
}
