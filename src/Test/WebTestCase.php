<?php
namespace App\Test;

use App\Entity\User;
use App\Entity\UserApiToken;
use Doctrine\ORM\EntityManagerInterface;

class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    protected function getUserApiToken($userEmail = 'admin@example.com'): string
    {
        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();

        /** @var $user \App\Model\User\User */
        $user = $em->getRepository(User::class)->findOneBy(['email' => $userEmail]);

        $token = new UserApiToken($user);

        $em->persist($token);
        $em->flush();

        return $token->getToken();
    }
}
