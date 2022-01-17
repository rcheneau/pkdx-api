<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PokemonTypeAffinity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PokemonTypeAffinity>
 *
 * @method PokemonTypeAffinity|null find($id, $lockMode = null, $lockVersion = null)
 * @method PokemonTypeAffinity|null findOneBy(array $criteria, array $orderBy = null)
 * @method PokemonTypeAffinity[]    findAll()
 * @method PokemonTypeAffinity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PokemonTypeAffinityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PokemonTypeAffinity::class);
    }
}
