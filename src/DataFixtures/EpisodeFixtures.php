<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use App\Entity\Episode;
use App\Entity\Season;
use App\Service\Slugify;
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
        $faker = Faker\Factory::create('en_US');
        $slugify = new Slugify();

        foreach (ProgramFixtures::PROGRAMS as $title => $data) {

            for ($i = 0; $i < 6; $i++) {
                $episodeFaker = new Episode();
                $episodeFaker->setTitle($faker->sentence($nbWords = 6, $variableNbWords = true));
                $episodeFaker->setNumber($i + 1);
                $episodeFaker->setSynopsis($faker->text($maxNbChars = 400));
                $episodeFaker->setSlug($slugify->generate($episodeFaker->getTitle()));
                $episodeFaker->setSeason($this->getReference('season_' . $i));

                $manager->persist($episodeFaker);
            }
        }

        $manager->flush();
    }
}
