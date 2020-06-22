<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use App\Entity\Episode;
use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 1; $i < 50; $i++){
            $episode = new Episode();
            $episode->setTitle($faker->domainName);
            $episode->setNumber($faker->randomDigit);
            $episode->setSynopsis($faker->text);
            $this->addReference('episode_' . $i, $episode);
            $episode->setSeason($this->getReference('season_' . random_int(1, 10)));
            $manager->persist($episode);
        }

        $manager->flush();
    }
}
