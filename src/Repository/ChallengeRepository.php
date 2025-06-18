<?php

namespace App\Repository;

use App\Entity\Challenge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Challenge>
 */
class ChallengeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Challenge::class);
    }

    public function save(Challenge $challenge): Challenge
    {
        $this->getEntityManager()->persist($challenge);
        $this->getEntityManager()->flush();

        return $challenge;
    }

    public function remove(Challenge $challenge): void
    {
        $this->getEntityManager()->remove($challenge);
        $this->getEntityManager()->flush();
    }

    public function findOneById($id): ?Challenge
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findAllChallengesForUser(UserInterface $user): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
