<?php


namespace App\DataFixtures;


use App\Entity\Actor;
use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }

    public function load(ObjectManager $manager)
    {

        $faker = Faker\Factory::create('en_US');

        foreach (ProgramFixtures::PROGRAMS as $title => $data) {

            for ($i = 0; $i < 6; $i++) {
                $seasonFaker = new Season();
                $seasonFaker->setNumber($i + 1);
                $seasonFaker->setDescription($faker->text($maxNbChars = 400));
                $seasonFaker->setYear($faker->year($max = 'now'));
                $seasonFaker->setProgram($this->getReference('program_' . $i));
                $this->setReference('season_' . $i, $seasonFaker);

                $manager->persist($seasonFaker);
            }
        }

        $manager->flush();
    }

}