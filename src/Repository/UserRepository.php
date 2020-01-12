<?php
namespace App\Repository;

use App\Model\User\Profile;
use App\Model\User\User;
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

    public function getProfileByEmail(string $email): Profile
    {
        return $this->getProfileBy('email', $email);
    }

    public function getProfileById(string $id): Profile
    {
        return $this->getProfileBy('id', $id);
    }

    public function getUserByApiToken(string $apiToken): User
    {
        $query = sprintf('SELECT NEW %s(u.id, u.email, u.name) 
            FROM App:UserApiToken t 
            JOIN t.user u
            WHERE t.token = :token',
            User::class
        );

        return $this->em
            ->createQuery($query)
            ->setParameter('token', $apiToken)
            ->getSingleResult();
    }

    public function getUserByEmail(string $email): User
    {
        $query = sprintf('SELECT NEW %s(u.id, u.email, u.name) 
            FROM App:UserApiToken t 
            JOIN t.user u
            WHERE t.email = :email',
            User::class
        );

        return $this->em
            ->createQuery($query)
            ->setParameter('email', $email)
            ->getSingleResult();
    }

    private function getProfileBy(string $field, string $value): Profile
    {
        $query = sprintf(
            'SELECT NEW %s(u.id, u.email, u.name) 
                FROM App:User u 
                WHERE u.%s = :value',
            Profile::class,
            $field
        );

        return $this->em
            ->createQuery($query)
            ->setParameter('value', $value)
            ->getSingleResult();
    }
}
