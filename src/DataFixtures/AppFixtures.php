<?php

namespace App\DataFixtures;

use App\Entity\Exercise;
use App\Entity\Record;
use App\Entity\User;
use App\Entity\UserApiToken;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //password: admin123
        $user = new User(
            'admin@example.com',
            '$argon2id$v=19$m=65536,t=4,p=1$bvEeQQRc3jztvvoslXCJuA$SBW1oAFjSdasyHcJEJApD9VxNY8J39EY/1YQceVvk7s',
        'John Doe'
        );

        $secondUser = new User(
            'second@example.com',
            '$argon2id$v=19$m=65536,t=4,p=1$bvEeQQRc3jztvvoslXCJuA$SBW1oAFjSdasyHcJEJApD9VxNY8J39EY/1YQceVvk7s',
            'Second User',
        );

        $exercise = new Exercise($user, 'Deadlift', [Exercise::ATTRIBUTE_REP, Exercise::ATTRIBUTE_WEIGHT]);

        $manager->persist($user);
        $manager->persist($secondUser);
        $manager->persist($exercise);

        $manager->flush();
    }
}
