<?php

namespace App\Controller\Configuration;

use App\Service\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/config/parametere')]
class ParametreController extends AbstractController
{

    #[Route(path: '/', name: 'app_config_parametre_index', methods: ['GET', 'POST'])]
    public function index(Request $request, Breadcrumb $breadcrumb): Response
    {
        $modules = [
            [
                'label' => 'Général',
                'icon' => 'bi bi-list',
                'href' => $this->generateUrl('app_config_parametre_ls', ['module' => 'config'])
            ],
            [
                'label' => 'Ressource humaine',
                'icon' => 'bi bi-truck',
                'href' => $this->generateUrl('app_config_parametre_ls', ['module' => 'rh'])
            ],
            [
                'label' => 'Gestion utilisateur',
                'icon' => 'bi bi-users',
                'href' => $this->generateUrl('app_config_parametre_ls', ['module' => 'utilisateur'])
            ],


        ];

        $breadcrumb->addItem([
            [
                'route' => 'app_default',
                'label' => 'Tableau de bord'
            ],
            [
                'label' => 'Paramètres'
            ]
        ]);

        return $this->render('config/parametre/index.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb
        ]);
    }


    #[Route(path: '/{module}', name: 'app_config_parametre_ls', methods: ['GET', 'POST'])]
    public function liste(Request $request, string $module): Response
    {
        /**
         * @todo: A déplacer dans un service
         */
        $parametres = [

            'utilisateur'=>[
                [
                    'label' => 'Groupes modules',
                    'id' => 'param_groupe_m',
                    'href' => $this->generateUrl('app_utilisateur_groupe_module_index')
                ],
                [
                    'label' => 'Module',
                    'id' => 'param_module',
                    'href' => $this->generateUrl('app_utilisateur_module_index')
                ],
                [
                    'label' => 'Permissions',
                    'id' => 'param_permission',
                    'href' => $this->generateUrl('app_utilisateur_permition_index')
                ],
            ],
            'rh'=>[
                [
                    'label' => 'Fonction',
                    'id' => 'param_categorie',
                    'href' => $this->generateUrl('app_parametre_fonction_index')
                ],
                [
                    'label' => 'Entreprise',
                    'id' => 'param_entreprise',
                    'href' => $this->generateUrl('app_parametre_entreprise_index')
                ],
                [
                    'label' => 'Direction',
                    'id' => 'param_direction',
                    'href' => $this->generateUrl('app_parametre_service_index')
                ],

                [
                    'label' => 'Employé',
                    'id' => 'param_client',
                    'href' => $this->generateUrl('app_utilisateur_employe_index')
                ],
              /*  [
                    'label' => 'Fournisseur',
                    'id' => 'param_fournisseur',
                    'href' => $this->generateUrl('app_rh_fournisseur_index')
                ],*/


            ],

            'config' => [
                [
                    'label' => 'Civilité',
                    'id' => 'param_article',
                    'href' => $this->generateUrl('app_parametre_civilite_index')
                ]
                ,
                [
                    'label' => 'Parametre',
                    'id' => 'param_p',
                    'href' => $this->generateUrl('app_parametre_parametre_configuration_index')
                ]

            ],


        ];


        return $this->render('config/parametre/liste.html.twig', ['links' => $parametres[$module] ?? []]);
    }
}