<?php

namespace App\DataFixtures;

use App\Entity\Avis;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class VideoFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $tab_video = array(
            'https://www.youtube.com/watch?v=mP_fnttJ5g0&t=453s' => 'Développeur Web : quotidien, salaire, parcours | Pool',
            'https://www.youtube.com/watch?v=xAe9bfgerH4' => 'Développeur Web : quotidien, salaire, parcours | Pool',
            'https://www.youtube.com/watch?v=H0UJvb8mI1o' => '\"TU VERRAS, DÉVELOPPEUR, C\'EST UN MÉTIER HYPER RECHERCHÉ !!!\"',
            'https://www.youtube.com/watch?v=QgjyOOa3jm0' => 'DEVENIR DÉVELOPPEUR WEB : études d\'ingénieur informatique, salaire, freelance, la réalité du métier',
            'https://www.youtube.com/watch?v=JRxntv23fK0' => 'Devenir Développeur #3 - Le salaire du développeur Web/Mobile ! Combien gagne un codeur ?',
            'https://www.youtube.com/watch?v=xdOvVQCqjJc' => 'Formation Complete Développeur Web',
            'https://www.youtube.com/watch?v=INWzYUiVI08' => 'Une journée random de développeur freelance (400€/jour)',
            'https://www.youtube.com/watch?v=ngjNLJBVIw0' => 'Recalé à mon 1er entretien de développeur web ! 😭',
            'https://www.youtube.com/watch?v=JfeuI6qDpNY' => 'Métier de l\'Informatique : Développeur Web');

        foreach ($tab_video as $i => $value) {
            $url = $i;
            $title = $value;
            $video = new Video();
            preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);
            $video
                ->setTitle($title)
                ->setUrl($matches[1]);
            $faker = Factory::create('fr_FR');
            for ($i = 0; $i < rand(1, 12); $i++) {
                $user = new User();
                $user
                    ->setUsername($faker->name)
                    ->setEmail($faker->email)
                    ->setPassword($faker->password);
                $avis = new Avis();
                $avis
                    ->setNote($faker->numberBetween($min = 1, $max = 10))
                    ->setCommentaire($faker->sentence($nbWords = 6, $variableNbWords = true))
                    ->setAvisVideo($video)
                    ->setUser($user);
                $manager->persist($user);
                $manager->persist($avis);
            }
            $manager->persist($video);
            $manager->flush();
        }

        /*
        for ($i = 0;
             $i < 10;
             $i++) {
            $faker = Factory::create('fr_FR');
            $faker->addProvider(new Youtube($faker));
            $video = new Video();
            $url = $faker->youtubeUri();
            preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);
            $video
                ->setTitle($faker->word)
                ->setUrl($matches[1]);
            $faker = Factory::create('fr_FR');
            for ($i = 0; $i < rand(1, 12); $i++) {
                $user = new User();
                $user
                    ->setUsername($faker->name)
                    ->setEmail($faker->email)
                    ->setPassword($faker->password);
                $avis = new Avis();
                $avis
                    ->setNote($faker->numberBetween($min = 1, $max = 5))
                    ->setCommentaire($faker->sentence($nbWords = 6, $variableNbWords = true))
                    ->setAvisVideo($video)
                    ->setUser($user);
                $manager->persist($user);
                $manager->persist($avis);
            }
            $manager->persist($video);
            $manager->flush();
        }*/

    }
}
