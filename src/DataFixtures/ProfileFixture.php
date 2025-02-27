<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfileFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $profile1 = new Profile();
        $profile1 -> setRs('Facebook');
        $profile1 ->setUrl('https://www.facebook.com/mehdi.baggar.52');

        $profile2 = new Profile();
        $profile2 -> setRs('twitter');
        $profile2 ->setUrl('https://x.com/baggar_elmehdji');

        $profile3 = new Profile();
        $profile3 -> setRs('GitHub');
        $profile3 ->setUrl('https://github.com/MehdiBaggar');





        $manager->persist($profile1);
        $manager->persist($profile2);
        $manager->persist($profile3);
        $manager->flush();
    }
}
