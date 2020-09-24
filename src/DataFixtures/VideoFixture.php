<?php

namespace App\DataFixtures;

use App\Entity\Avis;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\Youtube;

class VideoFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {

        for ($i = 0;
             $i < 10;
             $i++) {
            $faker = Factory::create('fr_FR');
            $faker->addProvider(new Youtube($faker));
            $video = new Video();
            $furl = $faker->youtubeUri();
            parse_str(parse_url($furl, PHP_URL_QUERY), $my_array_of_vars);
            $video
                ->setTitle($faker->word)
                ->setUrl($my_array_of_vars['v']);
            $faker = Factory::create('fr_FR');
            for ($i = 0; $i < rand(1, 12); $i++) {
                $user = new User();
                $user
                    ->setUsername($faker->name)
                    ->setEmail($faker->email)
                    ->setPassword($faker->password);
                $avis = new Avis();
                $avis
                    ->setNote($faker->numberBetween($min = 0, $max = 5))
                    ->setCommentaire($faker->sentence($nbWords = 6, $variableNbWords = true))
                    ->setAvisVideo($video)
                    ->setUser($user);
                $manager->persist($user);
                $manager->persist($avis);
            }
            $manager->persist($video);
            $manager->flush();
        }
    }
}
