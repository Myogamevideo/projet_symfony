<?php
namespace App\Controller;

use App\Repository\AvisRepository;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoAvisController extends AbstractController
{

    /**
     * @var VideoRepository
     */
    private $repoV;
    /**
     * @var AvisRepository
     */
    private $repoA;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * VideoAvisController constructor.
     * @param VideoRepository $repoV
     * @param AvisRepository $repoA
     * @param EntityManagerInterface $em
     */
    public function __construct(VideoRepository $repoV, AvisRepository $repoA, EntityManagerInterface $em)
    {
        $this->repoV = $repoV;
        $this->repoA = $repoA;
        $this->em = $em;
    }

    /**
     * @Route("/topVideoNombreAvis" , name="VideoAvisController.topVideoNombreAvis")
     * @return Response
     */
    public function topVideoNombreAvis(): Response
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

    /**
     * @Route("/topVideoNote" , name="VideoAvisController.topVideoNote")
     * @return Response
     */
    public function topVideoNote(): Response
    {
        $videos = $this->repoV->findAll();
        foreach ($videos as $video) {
            $video = $video->getId();
            $avis = $this->repoA->findIdVideoMoyenneAvis($video);
            $tab[$video] = $avis[1];
            arsort($tab);
        }
        foreach ($tab as $key => $val) {
            $video = $this->repoV->find($key);
            $tabvideo[$val] = $video;
        }
        dump($tab);
        dump($tabvideo);
        return $this->render('pages/TopVideoNote.html.twig', ['current_menu' => 'TopVideoNote', 'videos' => $tabvideo]);
    }

    /**
     * @Route("/uniqVideo/{id}" , name="VideoAvisController.uniqVideo")
     * @return Response
     */
    public function uniqVideo($id): Response
    {
        $video = $this->repoV->find($id);
        $avis = $this->repoA->findIdAllAvis($id);
        $nbavis = $this->repoA->findIdVideoNombreAvis($id);
        $avgavis = $this->repoA->findIdVideoMoyenneAvis($id);
        return $this->render('pages/UniqVideo.html.twig', ['current_menu' => 'UniqVideo', 'video' => $video , 'avis' => $avis , 'nbavis' => $nbavis, 'avgavis' => $avgavis]);
    }
}
