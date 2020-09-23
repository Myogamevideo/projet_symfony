<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\VideoRepository;
use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TopVideoNombreAvisController extends AbstractController
{


    /**
     * @var VideoRepository
     */
    private $repoV;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var AvisRepository
     */
    private $repoA;

    public function __construct(VideoRepository $repoV, AvisRepository $repoA, EntityManagerInterface $em)
    {
        $this->repoV = $repoV;
        $this->em = $em;
        $this->repoA = $repoA;
    }

    /**
     * @Route("/TopVideoNombreAvis" , name="TopVideoNombreAvis.index")
     * @return Response
     */
    public function index(): Response
    {
        /*
        $video = new Video();
        $video->setTitle("ccc")->setUrl("https://www.youtube.com/watch?v=Gv7EUDzq2Z8&t=18s");

        $avis = new Avis();
        $avis->setAvisVideo($video)->setNote(2);

        $am = $this->getDoctrine()->getManager();
        $am->persist($video);
        $am->persist($avis);
        $am->flush();
        */

        /*
        $repo = $this->getDoctrine()->getRepository(Video::class);
        dump($repo);
         */

        /*
        $video = $this->repo->find(1);
        $video = $this->repo->findAll();
        $video = $this->repo->findOneBy(['title' => 'aaaaaaaa']);
        */

        /*
        $video[0]->setTitle("ababab");
        $this->em->flush();
        */

        $videos = $this->repoV->findAll();
        foreach ($videos as $video) {
            $video = $video->getId();
            $avis = $this->repoA->findIdVideoNombreAvis($video);
            $tab[$video] = $avis[1];
            arsort($tab);
        }
        foreach ($tab as $key => $val) {
            $video = $this->repoV->find($key);
            $tabvideo[$val] = $video;
        }
        dump($tab);
        dump($tabvideo);
        return $this->render('pages/TopVideoNombreAvis.html.twig', ['current_menu' => 'TopVideoNombreAvis', 'videos' => $tabvideo]);

    }
}