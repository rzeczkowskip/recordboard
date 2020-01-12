<?php
namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

class UserRepository
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getAuthData(string $username): array
    {
        return $this->em
            ->createQuery('SELECT PARTIAL u.{id,password} FROM App:User u WHERE u.email = :username OR u.id = :username')
            ->setParameter('username', $username)
            ->getSingleResult(Query::HYDRATE_ARRAY);
    }

    public function getUserById(string $id): User
    {
        return $this->getUserBy('id', $id);
    }

    public function getUserByApiToken(string $apiToken): User
    {
        $token = $this->em
            ->createQuery('SELECT t,u
                FROM App:UserApiToken t 
                JOIN t.user u
                WHERE t.token = :token')
            ->setParameter('token', $apiToken)
            ->getSingleResult();

        return $token->getUser();
    }

    private function getUserBy(string $field, string $value): User
    {
        $query = sprintf(
            'SELECT u 
                FROM App:User u 
                WHERE u.%s = :value',
            $field
        );

        return $this->em
            ->createQuery($query)
            ->setParameter('value', $value)
            ->getSingleResult();
    }
}
