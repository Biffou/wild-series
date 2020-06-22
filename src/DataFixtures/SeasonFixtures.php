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
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 1; $i < 11; $i++){
            $season = new Season();
            $season->setNumber($i);
            $season->setYear($faker->year);
            $season->setDescription($faker->text);
            $this->addReference('season_' . $i, $season);
            $season->setProgram($this->getReference('program_' . random_int(0, 5)));
            $manager->persist($season);
        }

            $manager->flush();
    }

}