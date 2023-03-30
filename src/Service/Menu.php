<?php

namespace App\Service;

use App\Entity\ModuleGroupePermition;
use App\Entity\ParametreConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use function Symfony\Bundle\FrameworkBundle\Controller\redirectToRoute;

class Menu
{

    private $em;
    private $route;
    private $router;
    private $container;
    private $security;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack, RouterInterface $router,Security $security)
    {
        $this->em = $em;
        if ($requestStack->getCurrentRequest()) {
            $this->route = $requestStack->getCurrentRequest()->attributes->get('_route');
            $this->container = $router->getRouteCollection()->all();
            $this->security = $security;
        }
        //$this->router = $router;
       // $this->getPermission1();

    }

    public function listeModule()
    {
        $repo = $this->em->getRepository(ModuleGroupePermition::class)->afficheModule($this->security->getUser()->getGroupe()->getId());
        return $repo;
    }

    public function listeGroupeModule()
    {
        $repo = $this->em->getRepository(ModuleGroupePermition::class)->affiche($this->security->getUser()->getGroupe()->getId());

        return $repo;
    }

    public function findParametre()
    {
        $repo = $this->em->getRepository(ParametreConfiguration::class)->findOneBy(array('entreprise'=>$this->security->getUser()->getEmploye()->getEntreprise()));
       // dd($repo);
        return $repo;
    }
    /*public function getPermission1()
    {
        $repo = $this->em->getRepository(ModuleGroupePermition::class)->getPermission($this->security->getUser()->getGroupe()->getId(),$this->route);
        if(empty($repo)){

           // new RedirectResponse($this->router->generate($this->route))->redirectToRoute('app_utilisateur_groupe_index');

                return true;
           // }
        }else{
           // dd('false');
            return false;
        }
    }*/
    public function getPermission($lien)
    {
        $repo = $this->em->getRepository(ModuleGroupePermition::class)->getPermission($this->security->getUser()->getGroupe()->getId(),$lien);
         //dd($this->route);
        return $repo;
    }

    public function liste()
    {
        $repo = $this->em->getRepository(Groupe::class)->afficheGroupes();

        return $repo;
    }

    public function listeParent()
    {
        $repo = $this->em->getRepository(Groupe::class)->affiche();

        return $repo;
    }
//public function listeModule
    public function listeGroupe()
    {
        $array = [
            'module'=>'module',
            'agenda'=>'agenda',
            'typeClient'=>'typeClient',
            'groupe'=>'groupe',
            'parent'=>'parent',
            'parametre'=>'parametre',
            'typeSociete'=>'typeSociete',
            'acteConstitution'=>'acteConstitution',
            'dossierConstition'=>'dossierConstition',
            'user'=>'user',
            'app_utilisateur_user_groupe_index'=>'app_utilisateur_user_groupe_index',
            'employe'=>'employe',
            'categorie'=>'categorie',
            'dossierActeVente'=>'dossierActeVente',
            'acte'=>'acte',
            'typeActe'=>'typeActe',
            'client'=>'client',
            'workflow'=>'workflow',
            'courierArrive'=>'courierArrive',
            'courierDepart'=>'courierDepart',
            'courierInterne'=>'courierInterne',
            'calendar'=>'calendar',
            'documentTypeActe'=>'documentTypeActe',
        ];

        return $array ;
    }

}