<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

    /**
     * Finds a single User entity by a specific field.
     *
     * @param string $attribute The name of the field to search by.
     * @param string $value The value to search for in the specified field.
     *
     * @return User|null The found User entity or null if no User entity was found.
     * @throws NonUniqueResultException
     */
    public function findOneBySomeField(string $attribute, string $value): ?User
    {
        return $this->createQueryBuilder('u')
            ->where("u.$attribute = :$attribute")
            ->setParameter($attribute, $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $email The email address to search for
     * @return User|null The user with the specified email address or null if not found
     * @throws NonUniqueResultException
     */
    public function findUserByEmail(string $email): ?User
    {
        return $this->findOneBySomeField('email', $email);
    }

    /**
     * Finds a user by their pseudo.
     *
     * @param string $pseudo The pseudo of the user to find.
     * @return User|null The User entity if found, null otherwise.
     * @throws NonUniqueResultException
     */
    public function findUserByPseudo(string $pseudo): ?User
    {
        return $this->findOneBySomeField('pseudo', $pseudo);
    }

    /**
     *
     * @param string $token The reset token to search for.
     *
     * @return User|null The User entity if a match is found, otherwise null.
     * @throws NonUniqueResultException
     */
    public function findOneByResetToken(string $token): ?User
    {
        return $this->findOneBySomeField('resetToken', $token);
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
}
