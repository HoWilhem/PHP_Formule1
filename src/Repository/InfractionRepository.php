<?php

namespace App\Repository;

use App\Entity\Infraction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class InfractionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Infraction::class);
    }

    /**
     * Recherche des infractions selon les filtres (pilote, écurie, date)
     *
     * @param int|null $piloteId
     * @param int|null $ecurieId
     * @param string|null $date
     * @return Infraction[]
     */
    public function search(?int $piloteId = null, ?int $ecurieId = null, ?string $date = null): array
    {
        $qb = $this->createQueryBuilder('i')
            ->leftJoin('i.pilote', 'p')
            ->leftJoin('i.ecurie', 'e')
            ->addSelect('p', 'e');

        if ($piloteId) {
            $qb->andWhere('p.id = :piloteId')
               ->setParameter('piloteId', $piloteId);
        }

        if ($ecurieId) {
            $qb->andWhere('e.id = :ecurieId')
               ->setParameter('ecurieId', $ecurieId);
        }

        if ($date) {
            try {
                $qb->andWhere('i.date = :date')
                   ->setParameter('date', new \DateTime($date));
            } catch (\Exception $e) {
                // Ignore si la date est invalide
            }
        }

        return $qb->orderBy('i.date', 'DESC')->getQuery()->getResult();
    }
}
