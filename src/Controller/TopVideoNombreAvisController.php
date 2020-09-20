<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TopVideoNombreAvisController extends AbstractController{

    /**
     * @Route("/TopVideoNombreAvis" , name="TopVideoNombreAvis.index")
     * @return Response
     */

    public function index(): Response{
        return $this->render('pages/TopVideoNombreAvis.html.twig', ['current_menu' => 'TopVideoNombreAvis']);

    }
}