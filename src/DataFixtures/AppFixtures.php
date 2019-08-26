<?php

namespace App\DataFixtures;

use App\Entity\Exercise;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User(
            'admin@example.com',
            '$argon2id$v=19$m=65536,t=4,p=1$bvEeQQRc3jztvvoslXCJuA$SBW1oAFjSdasyHcJEJApD9VxNY8J39EY/1YQceVvk7s',
        'John Doe'
        );

        $exercise = new Exercise('Deadlift', [Exercise::ATTRIBUTE_REP, Exercise::ATTRIBUTE_WEIGHT]);

        $manager->persist($user);
        $manager->persist($exercise);

        $manager->flush();
    }
}
