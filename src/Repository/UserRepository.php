<?php
namespace App\Repository;

use App\DTO\User\Profile;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getAuthData(string $username): array
    {
        return $this->getEntityManager()
            ->createQuery('SELECT PARTIAL u.{id,email,password} FROM App:User u WHERE u.email = :email')
            ->setParameter('email', $username)
            ->getSingleResult(Query::HYDRATE_ARRAY);
    }

    public function getProfileByEmail(string $email): Profile
    {
        return $this->getProfileBy('email', $email);
    }

    public function getProfileById(string $id): Profile
    {
        return $this->getProfileBy('id', $id);
    }

    public function getUserByApiToken(string $apiToken): \App\DTO\User\User
    {
        return $this->getEntityManager()
            ->createQuery(sprintf(
                <<<SQL
                SELECT NEW %s(u.id, u.email, u.name) 
                FROM App:UserApiToken t 
                JOIN t.user u
                WHERE t.token = :token
                SQL,
                \App\DTO\User\User::class
            ))
            ->setParameter('token', $apiToken)
            ->getSingleResult();
    }

    private function getProfileBy(string $field, string $value): Profile
    {
        return $this->getEntityManager()
            ->createQuery(sprintf(
                'SELECT NEW %s(u.id, u.email, u.name) 
                FROM App:User u 
                WHERE u.%s = :value',
                Profile::class,
                $field
            ))
            ->setParameter('value', $value)
            ->getSingleResult();
    }
}
