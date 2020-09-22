<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TopVideoNombreAvisController extends AbstractController{

    /**
     * @Route("/TopVideoNombreAvis" , name="TopVideoNombreAvis.index")
     * @return Response
     */

    public function index(): Response{

        $video = new Video();
        $video->setTitle("aaaaaaaa")->setUrl("https://www.youtube.com/watch?v=Gv7EUDzq2Z8&t=18s");

        $avis = new Avis();
        $avis->setAvisVideo($video)->setCommentaire("lol")->setNote(3);

        $this->getDoctrine()->getManager();

        return $this->render('pages/TopVideoNombreAvis.html.twig', ['current_menu' => 'TopVideoNombreAvis']);

    }
}