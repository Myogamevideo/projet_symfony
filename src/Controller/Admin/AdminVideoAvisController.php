<?php

namespace App\Controller\Admin;

use App\Entity\Avis;
use App\Entity\Video;
use App\Form\AvisType;
use App\Form\VideoType;
use App\Repository\AvisRepository;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminVideoAvisController extends AbstractController
{
    private $repoAvis;
    private $objectManager;
    private $repoVideo;

    /**
     * AdminVideoAvisController constructor.
     * @param AvisRepository $repoAvis
     * @param EntityManagerInterface $objectManager
     * @param VideoRepository $repoVideo
     */
    public function __construct(AvisRepository $repoAvis, EntityManagerInterface $objectManager , VideoRepository $repoVideo)
    {

        $this->repoAvis = $repoAvis;
        $this->objectManager = $objectManager;
        $this->repoVideo = $repoVideo;
    }

    /**
     * @Route("/admin",name="admin.avi.index")
     * @return Response
     */
    public function index()
    {
        $user = $this->getUser();
        $avis = $this->repoAvis->findIdUserAllAvis($user);
        foreach ($avis as $avi){
            $video = $avi->getAvisVideo()->getTitle();
            $tabavis[$video] = $avi;
        }
        if(!empty($tabavis)){
            return $this->render('admin/avis/index.html.twig', compact('tabavis'));
        }else{
            $this->addFlash('danger','Veuillez déposer un avis pour accéder à cette page !');
            return $this->redirectToRoute('home');
        }

    }

    /**
     * @Route("/admin/edition/{id}",name="admin.avi.edit")
     * @param $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit($id, Request $request)
    {
        if($this->repoAvis->find($id) == null){
            $this->addFlash('danger','Zone indisponible !');
            return $this->redirectToRoute('home');
        }else{
            $avis = $this->repoAvis->find($id);
        }
        if($avis->getUser() != $this->getUser()){
            $this->addFlash('danger','Zone indisponible !');
            return $this->redirectToRoute('home');
        }
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            $this->objectManager->flush();
            $this->addFlash('success','Votre avis a été édité avec succès !');
            return $this->redirectToRoute('admin.avi.index');
        }
        $video = $avis->getAvisVideo();
        return $this->render('admin/avis/edit.html.twig', ['video' => $video, 'avis' => $avis, 'form' => $form->createView()]);
    }

    /**
     * @Route("/admin/suppression/{id}",name="admin.avi.del")
     * @param $id
     * @return Response
     */
    public function del($id)
    {
        if($this->repoAvis->find($id) == null){
            $this->addFlash('danger','Zone indisponible !');
            return $this->redirectToRoute('home');
        }else{
            $avis = $this->repoAvis->find($id);
        }
        if($avis->getUser() != $this->getUser()){
            $this->addFlash('danger','Zone indisponible !');
            return $this->redirectToRoute('home');
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($avis);
        $entityManager->flush();
        $this->addFlash('success','Votre avis a été supprimé avec succès !');
        return $this->redirectToRoute('admin.avi.index');
    }

    /**
     * @Route("/admin/creation",name="admin.avi.create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function add(Request $request)
    {
        if($this->getUser() == null){
            $this->addFlash('danger','Zone indisponible !');
            return $this->redirectToRoute('home');
        }

        $newvideo = new Video();
        $formvideo = $this->createForm(VideoType::class, $newvideo);
        $newavis = new Avis();
        $formavis = $this->createForm(AvisType::class, $newavis);
        if ($request->isMethod('POST')) {
            $formvideo->handleRequest($request);
            $formavis->handleRequest($request);
            if ($formvideo->isValid() and $formavis->isValid() and $formvideo->isSubmitted() and $formavis->isSubmitted()) {
                $url = $newvideo->getUrl();
                preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);
                if(!empty($matches[1])){
                    $url = $matches[1];
                }else{
                    $this->addFlash('danger','Votre vidéo n\'a pas été trouvée !');
                    return $this->redirectToRoute('home');
                }
                if($this->repoVideo->findUrlVideo($url) == null){
                    $newvideo->setUrl($url);
                    $title = $this->get_youtube_title($url);
                    $newvideo->setTitle($title);
                    $this->objectManager->persist($newvideo);
                    $this->objectManager->flush();
                    $newavis->setAvisVideo($newvideo);
                    $newavis->setUser($this->getUser());
                    $this->objectManager->persist($newavis);
                    $this->objectManager->flush();
                    $this->addFlash('success','Votre avis a été ajouté avec succès !');
                    $id = $newvideo->getId();
                    return $this->redirectToRoute('VideoAvisController.uniqVideo',['id' => $id]);
                }else{
                    $video = $this->repoVideo->findUrlVideo($url);
                    $newavis->setAvisVideo($video);
                    $newavis->setUser($this->getUser());
                    $this->objectManager->persist($newavis);
                    $this->objectManager->flush();
                    $this->addFlash('success','Votre avis a été ajouté avec succès (La video était déjà dans notre base de donnée) !');
                    $id = $video->getId();
                    return $this->redirectToRoute('VideoAvisController.uniqVideo',['id' => $id]);
                }
            }
        }
        return $this->render('admin/avis/add.html.twig', ['formvideo' => $formvideo->createView(), 'formavis' => $formavis->createView()]);
    }

    function get_youtube_title($url){
        $videoid = $url;
        $apikey = 'AIzaSyCYfKILFM2DR3CUeH-fkzpcb5Q41jUFeOE';
        $html = 'https://www.googleapis.com/youtube/v3/videos?id='.$videoid.'&key='.$apikey.'&part=snippet';
        dump($html);
        $response = file_get_contents($html);
        dump($response);
        $decoded = json_decode($response, true);
        dump($decoded);
        foreach ($decoded['items'] as $items) {
            $title = $items['snippet']['title'];
            return $title;
        }
    }
}