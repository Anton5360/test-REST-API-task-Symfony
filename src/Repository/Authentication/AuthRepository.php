<?php

namespace App\Repository\Authentication;

use App\Entity\Auth;
use App\Entity\User;
use App\Repository\Authentication\Interfaces\AuthRepositoryInterface;
use App\Repository\Traits\EntityManagerShortcutsTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Auth>
 */
class AuthRepository extends ServiceEntityRepository implements AuthRepositoryInterface
{
    use EntityManagerShortcutsTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Auth::class);
    }

    public function findByUser(User $user): ?Auth
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function create(User $user, string $token): void
    {
        $auth = (new Auth())
            ->setUser($user)
            ->setToken($token);

        $this->persistAndFlush($auth);
    }
}
