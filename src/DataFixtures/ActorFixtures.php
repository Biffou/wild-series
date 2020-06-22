<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\Self_;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    const ACTORS = [
        'Andrew Lincoln' => ['program_0', 'program_5'],
        'Norman Reedus' => ['program_0'],
        'Lauren Cohan' => ['program_0'],
        'Danai Gurira' => ['program_0'],
    ];

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('en_US');
        for ($i = 4; $i < 55; $i++){
        $actor = new Actor();
        $actor->setName($faker->name());
        $this->addReference('actor_' . $i, $actor);
        $actor->addProgram($this->getReference('program_' . random_int(0, 5)));
        $manager->persist($actor);
        }

        $i = 0;
        foreach (Self::ACTORS as $actorName => $data) {
            $actor= new Actor();
            $actor->setName($actorName);
            foreach ($data as $program) {
                $actor->addProgram($this->getReference($program));
            }
            $manager->persist($actor);
            $this->addReference('actor_' . $i, $actor);
            $i++;
        }
            $manager->flush();
        }
}
