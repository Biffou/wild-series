<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\Program;
use App\Service\Slugify;
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
        $faker = Faker\Factory::create('fr_FR');
        $slugify = New Slugify();

        for ($i = 0; $i < 50; $i++) {
            $actor = new Actor();
            $actor->setName($faker->name);
            $this->addReference('actor_' . $i, $actor);
            $actor->addProgram($this->getReference('program_' . $faker->numberBetween(0,5)));
            $slug = $slugify->generate($actor->getName());
            $actor->setSlug($slug);
            $manager->persist($actor);
        }
        $manager->flush();

    }
}
