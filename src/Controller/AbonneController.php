<?php

namespace App\Controller;

use App\Entity\Abonne;
use App\Form\AbonneType;
use App\Repository\AbonneRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/abonne')]
class AbonneController extends AbstractController
{
    #[Route('/', name: 'app_abonne_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
              ->add('firstName', TextColumn::class, ['label' => 'Nom'])
              ->add('email', TextColumn::class, ['label' => 'Email'])
        ->createAdapter(ORMAdapter::class, [
            'entity' => Abonne::class,
        ])
        ->setName('dt_app_abonne');


        $renders = [
            'edit' =>  new ActionRender(function () {
                return false;
            }),
            'show' => new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return false;
            }),
        ];


        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }

        if ($hasActions) {
            $table->add('id', TextColumn::class, [
                'label' => 'Actions'
                , 'orderable' => false
                ,'globalSearchable' => false
                ,'className' => 'grid_row_actions'
                , 'render' => function ($value, Abonne $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                        'target' => '#exampleModalSizeLg2',

                        'actions' => [
                            'edit' => [
                            'url' => $this->generateUrl('app_abonne_edit', ['id' => $value])
                            , 'ajax' => true
                            , 'icon' => '%icon% bi bi-pen'
                            , 'attrs' => ['class' => 'btn-default']
                            , 'render' => $renders['edit']
                        ],
                        'delete' => [
                            'target' => '#exampleModalSizeNormal',
                            'url' => $this->generateUrl('app_abonne_delete', ['id' => $value])
                            , 'ajax' => true
                            , 'icon' => '%icon% bi bi-trash'
                            , 'attrs' => ['class' => 'btn-main']
                            ,  'render' => $renders['delete']
                        ],
                              'delete' => [
                        'target' => '#exampleModalSizeNormal',
                        'url' => $this->generateUrl('app_abonne_show', ['id' => $value])
                        , 'ajax' => true
                        , 'icon' => '%icon% bi bi-eye'
                        , 'attrs' => ['class' => 'btn-main']
                        ,  'render' => $renders['show']
                    ]
                    ]

                    ];
                    return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                }
            ]);
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('abonne/index.html.twig', [
            'datatable' => $table
        ]);
    }

    #[Route('/new', name: 'app_abonne_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AbonneRepository $abonneRepository, FormError $formError): Response
    {
        $abonne = new Abonne();
        $form = $this->createForm(AbonneType::class, $abonne, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_abonne_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_abonne_index');




            if ($form->isValid()) {

                $abonneRepository->save($abonne, true);
                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);


            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                if (!$isAjax) {
                  $this->addFlash('warning', $message);
                }

            }


            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }


        }

        return $this->renderForm('abonne/new.html.twig', [
            'abonne' => $abonne,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_abonne_show', methods: ['GET'])]
    public function show(Abonne $abonne): Response
    {
        return $this->render('abonne/show.html.twig', [
            'abonne' => $abonne,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_abonne_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Abonne $abonne, AbonneRepository $abonneRepository, FormError $formError): Response
    {

        $form = $this->createForm(AbonneType::class, $abonne, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_abonne_edit', [
                    'id' =>  $abonne->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_abonne_index');


            if ($form->isValid()) {

                $abonneRepository->save($abonne, true);
                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);


            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                if (!$isAjax) {
                  $this->addFlash('warning', $message);
                }

            }


            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('abonne/edit.html.twig', [
            'abonne' => $abonne,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_abonne_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Abonne $abonne, AbonneRepository $abonneRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'app_abonne_delete'
                ,   [
                        'id' => $abonne->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $abonneRepository->remove($abonne, true);

            $redirect = $this->generateUrl('app_abonne_index');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut'   => 1,
                'message'  => $message,
                'redirect' => $redirect,
                'data' => $data
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }
        }

        return $this->renderForm('abonne/delete.html.twig', [
            'abonne' => $abonne,
            'form' => $form,
        ]);
    }
}
