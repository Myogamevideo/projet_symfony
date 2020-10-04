<?php
namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use App\Repository\UserRepository;
use App\Repository\VideoRepository;
use ArrayObject;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @var UserRepository
     */
    private $repoU;

    /**
     * VideoAvisController constructor.
     * @param VideoRepository $repoV
     * @param AvisRepository $repoA
     * @param UserRepository $repoU
     * @param EntityManagerInterface $em
     */
    public function __construct(VideoRepository $repoV, AvisRepository $repoA, UserRepository $repoU , EntityManagerInterface $em)
    {
        $this->repoV = $repoV;
        $this->repoA = $repoA;
        $this->em = $em;
        $this->repoU = $repoU;
    }

    /**
     * @Route("/topVideoNombreAvis" , name="VideoAvisController.topVideoNombreAvis")
     * @return Response
     */
    public function topVideoNombreAvis(): Response
    {
        $videos = $this->repoV->findAll();
        foreach ($videos as $video) {
            $video = $video->getId();
            $avis = $this->repoA->findIdVideoNombreAvis($video);
            $tab[$video] = $avis[1];
            arsort($tab);
        }
        foreach ($tab as $key => $val) {
            $video = $this->repoV->find($key);
            $tabvideo[] = array($video,$val);
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
            $tabvideo[] = array($video,$val);
        }
        dump($tab);
        dump($tabvideo);
        return $this->render('pages/TopVideoNote.html.twig', ['current_menu' => 'TopVideoNote', 'videos' => $tabvideo]);
    }

    /**
     * @Route("/unique/{id}" , name="VideoAvisController.uniqVideo")
     * @param $id
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function uniqVideo($id, Request $request, PaginatorInterface $paginator): Response
    {
        if($this->repoV->find($id) == null){
            $this->addFlash('danger','Votre vidéo n\'a pas été trouvée !');
            return $this->redirectToRoute('home');
        }else{
            $video = $this->repoV->find($id);
            $avis = $this->repoA->findIdAllAvis($id);
        }
        foreach ($avis as $avi){
            $user = $avi->getUser()->getUsername();
            $tabavis[] = array($user,$avi);
        }
        dump($tabavis);

        $req = $paginator->paginate(
            $tabavis,
            $request->query->getInt('page', 1),
            5
        );

        $newavis = new Avis();
        $form = $this->createForm(AvisType::class, $newavis);
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            $newavis->setAvisVideo($video);
            $newavis->setUser($this->getUser());
            $this->em->persist($newavis);
            $this->em->flush();
            $this->addFlash('success','Votre avis a été ajouté avec succès !');
            return $this->redirectToRoute('VideoAvisController.uniqVideo',['id' => $id]);
        }

        $nbavis = $this->repoA->findIdVideoNombreAvis($id);
        $avgavis = $this->repoA->findIdVideoMoyenneAvis($id);
        return $this->render('pages/UniqVideo.html.twig', ['current_menu' => 'UniqVideo', 'video' => $video , 'avis' => $req , 'nbavis' => $nbavis, 'avgavis' => $avgavis, 'newavis' =>$newavis, 'form' => $form->createView()]);
    }

    /**
     * @Route("/recherche" , name="_search")
     * @param Request $request
     * @return RedirectResponse
     */
    public function search(Request $request)
    {
        $url = $request->request->get('search');
        preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);
        if(!empty($matches[1])){
            $url = $matches[1];
        }else{
            $this->addFlash('danger','Votre vidéo n\'a pas été trouvée !');
            return $this->redirectToRoute('home');
        }
        if($this->repoV->findUrlVideo($url) == null){
            $this->addFlash('danger','Votre vidéo n\'a pas été trouvée !');
            return $this->redirectToRoute('home');
        }else{
            $video = $this->repoV->findUrlVideo($url);
        }
        return $this->redirectToRoute('VideoAvisController.uniqVideo',['id' => $video->getId()]);
    }



}
