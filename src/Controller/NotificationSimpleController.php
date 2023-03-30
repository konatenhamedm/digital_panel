<?php

namespace App\Controller;

use App\Entity\NotificationSimple;
use App\Form\NotificationSimpleType;
use App\Repository\NotificationSimpleRepository;
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

#[Route('/notification/simple')]
class NotificationSimpleController extends AbstractController
{
    #[Route('/', name: 'app_notification_simple_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('titre', TextColumn::class, ['label' => 'Titre'])
            ->add('content', TextColumn::class, ['label' => 'Message'])
        ->createAdapter(ORMAdapter::class, [
            'entity' => NotificationSimple::class,
        ])
        ->setName('dt_app_notification_simple');

        $renders = [
            'edit' =>  new ActionRender(function () {
                return false;
            }),
            'delete' => new ActionRender(function () {
                return false;
            }),
            'show' => new ActionRender(function () {
                return true;
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
                , 'render' => function ($value, NotificationSimple $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                        'target' => '#exampleModalSizeLg2',

                        'actions' => [
                            'edit' => [
                            'url' => $this->generateUrl('app_notification_simple_edit', ['id' => $value])
                            , 'ajax' => true
                            , 'icon' => '%icon% bi bi-pen'
                            , 'attrs' => ['class' => 'btn-default']
                            , 'render' => $renders['edit']
                        ],
                        'delete' => [
                            'target' => '#exampleModalSizeNormal',
                            'url' => $this->generateUrl('app_notification_simple_delete', ['id' => $value])
                            , 'ajax' => true
                            , 'icon' => '%icon% bi bi-trash'
                            , 'attrs' => ['class' => 'btn-main']
                            ,  'render' => $renders['delete']
                        ],
                            'show' => [
                                'target' => '#exampleModalSizeNormal',
                                'url' => $this->generateUrl('app_notification_simple_delete', ['id' => $value])
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


        return $this->render('notification_simple/index.html.twig', [
            'datatable' => $table
        ]);
    }

    #[Route('/new', name: 'app_notification_simple_new', methods: ['GET', 'POST'])]
    public function new(Request $request, NotificationSimpleRepository $notificationSimpleRepository, FormError $formError): Response
    {
        $notificationSimple = new NotificationSimple();
        $form = $this->createForm(NotificationSimpleType::class, $notificationSimple, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_notification_simple_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_notification_simple_index');




            if ($form->isValid()) {
                $notificationSimple->setDateCreation(new \DateTime());
                $notificationSimpleRepository->save($notificationSimple, true);
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

        return $this->renderForm('notification_simple/new.html.twig', [
            'notification_simple' => $notificationSimple,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_notification_simple_show', methods: ['GET'])]
    public function show(NotificationSimple $notificationSimple): Response
    {
        return $this->render('notification_simple/show.html.twig', [
            'notification_simple' => $notificationSimple,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_notification_simple_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NotificationSimple $notificationSimple, NotificationSimpleRepository $notificationSimpleRepository, FormError $formError): Response
    {

        $form = $this->createForm(NotificationSimpleType::class, $notificationSimple, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_notification_simple_edit', [
                    'id' =>  $notificationSimple->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_notification_simple_index');


            if ($form->isValid()) {

                $notificationSimpleRepository->save($notificationSimple, true);
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

        return $this->renderForm('notification_simple/edit.html.twig', [
            'notification_simple' => $notificationSimple,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_notification_simple_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, NotificationSimple $notificationSimple, NotificationSimpleRepository $notificationSimpleRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'app_notification_simple_delete'
                ,   [
                        'id' => $notificationSimple->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $notificationSimpleRepository->remove($notificationSimple, true);

            $redirect = $this->generateUrl('app_notification_simple_index');

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

        return $this->renderForm('notification_simple/delete.html.twig', [
            'notification_simple' => $notificationSimple,
            'form' => $form,
        ]);
    }
}
