<?php

namespace App\DataFixtures;

use App\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JobFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = [
            'Développeur Web',
            'Ingénieur Data',
            'Chef de Projet IT',
            'Administrateur Systèmes',
            'Designer UX/UI',
            'Consultant en Cybersécurité',
            'Analyste Business',
            'Spécialiste Cloud',
            'Ingénieur DevOps',
            'Technicien Réseau'
        ];
        for ($i=0;$i<count($data);$i++){
            $job = new Job();
            $job->setDesignation($data[$i]);
            $manager->persist($job);
        }


        $manager->flush();
    }
}
