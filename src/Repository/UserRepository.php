<?php

namespace App\Repository;

use App\DataTransferObjects\API\V1\Authentication\RegisterPayloadDTO;
use App\DataTransferObjects\API\V1\Profile\UpdateUserDTO;
use App\Entity\Auth;
use App\Entity\User;
use App\Repository\Interfaces\UserRepositoryInterface;
use App\Repository\Traits\EntityManagerShortcutsTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserRepositoryInterface
{
    use EntityManagerShortcutsTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function create(RegisterPayloadDTO $payload): void
    {
        $user = (new User())
            ->setEmail($payload->email)
            ->setPassword($payload->password)
            ->setName($payload->name);

        $this->persistAndFlush($user);
    }

    public function update(User $user, UpdateUserDTO $payload): void
    {
        $user->setName($payload->name);

        $this->persistAndFlush($user);
    }

    public function delete(User $user): void
    {
        $this->removeAndFlush($user);
    }

    public function findOneByAuthToken(string $token): ?User
    {
        return $this->createQueryBuilder('u')
            ->leftJoin(
                Auth::class,
                'a',
                Join::WITH,
                'a.user = u.id'
            )
            ->andWhere('a.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

        public function findOneByEmailField(string $email): ?User
        {
            return $this->createQueryBuilder('u')
                ->andWhere('u.email = :email')
                ->setParameter('email', $email)
                ->getQuery()
                ->getOneOrNullResult();
        }

    public function findOneByIdField(int $id): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
