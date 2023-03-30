<?php
declare(strict_types=1);

namespace App\Controller;


use App\Entity\Abonne;
use App\Entity\Favorie;
use App\Repository\AbonneRepository;
use App\Repository\FavorieRepository;
use App\Repository\NotificationRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use function Symfony\Component\String\__toString;

class ApiController extends AbstractController
{

    #[Route('/api/favorie/{id}/liste', name: 'app_favorie_liste',methods: 'GET')]
    public function indexListeFavories($id,FavorieRepository $repository): Response
    {
        $favories = $repository->findFavorieByUser($id);
//dd($favories);
        $data = [];

        foreach ($favories as $favorie) {
            $data[] = [
                'id' => $favorie['id'],
                'image' => $favorie['image'],
                'lien' => $favorie['lien'],
                'dateCreation' => $favorie['dateCreation'],
            ];
        }

        //  dd(json($data));


        return $this->json($data);

        // dd($repository->findAll());

        // $this->respond($data);
    }

    #[Route('/api/notification/{id}/liste', name: 'app_notification',methods: 'GET')]
    public function index($id,NotificationRepository $repository): Response
    {
        $notifications = $repository->findNotificationByUser($id);

        $data = [];

        foreach ($notifications as $notification) {
            $data[] = [
                'id' => $notification->getId(),
                'titre' => $notification->getTitre(),
                'content' => $notification->getContent(),
                'etat' => $notification->isEtat(),
                'dateCreation' => $notification->getDateCreation(),
            ];
        }

      //  dd(json($data));


        return $this->json($data);

       // dd($repository->findAll());

       // $this->respond($data);
    }


    #[Route('/api/abonne/create', name: 'app_create', methods: ['GET', 'POST'])]
    public function Abonnement(Request $request,AbonneRepository $abonneRepository):Response
    {
        $abonne = new Abonne();

        //dd($request->request);
        $abonne->setFirstName($request->get("nom"));
        $abonne->setSecondName($request->get("prenom"));
        $abonne->setEmail($request->get("email"));
        $abonne->setActive(1);
        $abonne->setConfirmed(1);
        $abonne->setDateCreated(new \DateTime());
        if($abonneRepository->findBy(array('email'=>$request->get("email"))) > 0){
            $codeStatut = 400;
            $data = [
                'message'=>"Cet abonné existe deja en base",
                'codeStatut'=>400
            ];
        }else{
            $abonneRepository->save($abonne,true);
            $data = [
                'message'=>"Enregistrement éffectué avec succes",
                'codeStatut'=>200
            ];
        }

        return  $this->json($data);

    }

    #[Route('/api/desabonne/{email}/delete', name: 'app_favorie_delete', methods: ['DELETE'])]
    public function desabonne($email,AbonneRepository $Repository): Response
    {

        $desabonne = $Repository->findBy(array('email'=>$email));

        if (!$desabonne) {
            $data = [
                'message'=>"Il n y a aucune enregistrement",
                'codeStatut'=>400,
            ];
            return $this->json($data);
        }

        $Repository->remove($desabonne,true);

        $data = [
            'message'=>"Vous vous êtes désabonné avec success",
            'codeStatut'=>200,
        ];
        return $this->json($data);
    }

    #[Route('/api/favorie/create', name: 'app_favorie_create', methods: ['GET', 'POST'])]
    public function Favorie(Request $request,FavorieRepository $Repository,UserRepository $userRepository,PostRepository $postRepository):Response
    {
        $favorie = new Favorie();

        //dd($request->request);
        $favorie->setImage($request->get("image"));
        $favorie->setPost($postRepository->find($request->get("post")));
        $favorie->setUser($userRepository->find($request->get("user")));
        $favorie->setLien($request->get("lien"));
        $favorie->setDateCreation(new \DateTime());
        $Repository->save($favorie,true);
        $data = [
            'message'=>"Enregistrement éffectué avec success",
            'codeStatut'=>200,
        ];

        return  $this->json($data);

    }

    #[Route('/api/favorie/{id}/delete', name: 'app_favorie_delete', methods: ['DELETE'])]
    public function delete($id,FavorieRepository $Repository): Response
    {

        $favorie = $Repository->find($id);

        if (!$favorie) {
            $data = [
                'message'=>"Il n y a aucune enregistrement",
                'codeStatut'=>400,
            ];
            return $this->json($data);
        }

        $Repository->remove($favorie,true);

        $data = [
            'message'=>"Enregistrement éffectué avec success",
            'codeStatut'=>200,
        ];
        return $this->json($data);
    }
}
