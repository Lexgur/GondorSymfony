<?php

namespace App\Repository;

use App\Entity\Exercise;
use App\Entity\MuscleGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exercise>
 */
class ExerciseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exercise::class);
    }

    public function findOneById($id): ?Exercise
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function fetchByMuscleGroup(MuscleGroup $group): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.muscleGroup = :muscle_group')
            ->setParameter('muscle_group', $group)
            ->getQuery()
            ->getResult();
    }

    public function findAllByIds(array $ids): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

}
