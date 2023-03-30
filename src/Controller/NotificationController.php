<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Form\NotificationType;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
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

#[Route('/notification')]
class NotificationController extends AbstractController
{
    #[Route('/', name: 'app_notification_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('titre', TextColumn::class, ['label' => 'Titre'])
            ->add('content', TextColumn::class, ['label' => 'Message'])
        ->createAdapter(ORMAdapter::class, [
            'entity' => Notification::class,
        ])
        ->setName('dt_app_notification');

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
                , 'render' => function ($value, Notification $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                        'target' => '#exampleModalSizeLg2',

                        'actions' => [
                            'edit' => [
                            'url' => $this->generateUrl('app_notification_edit', ['id' => $value])
                            , 'ajax' => true
                            , 'icon' => '%icon% bi bi-pen'
                            , 'attrs' => ['class' => 'btn-default']
                            , 'render' => $renders['edit']
                        ],
                        'delete' => [
                            'target' => '#exampleModalSizeNormal',
                            'url' => $this->generateUrl('app_notification_delete', ['id' => $value])
                            , 'ajax' => true
                            , 'icon' => '%icon% bi bi-trash'
                            , 'attrs' => ['class' => 'btn-main']
                            ,  'render' => $renders['delete']
                        ],
                            'show' => [
                                'target' => '#exampleModalSizeNormal',
                                'url' => $this->generateUrl('app_notification_show', ['id' => $value])
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


        return $this->render('notification/index.html.twig', [
            'datatable' => $table
        ]);
    }

    #[Route('/new', name: 'app_notification_new', methods: ['GET', 'POST'])]
    public function new(Request $request, NotificationRepository $notificationRepository,UserRepository $repository,FormError $formError): Response
    {
        $notification = new Notification();
        $form = $this->createForm(NotificationType::class, $notification, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_notification_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();
        $users = $repository->findAll();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_notification_index');
            //dd($form->getData()->getTitre());



            if ($form->isValid()) {


                foreach ($users as $ele) {
                    $notificationUser = new Notification();
                    $notificationUser->setUser($ele);
                    $notificationUser->setTitre($form->getData()->getTitre());
                    $notificationUser->setContent($form->getData()->getContent());
                    $notificationUser->setEtat(false) ;
                    $notificationUser->setDateCreation(new \DateTime()) ;
                    $notificationRepository->save($notificationUser, true);
                }


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

        return $this->renderForm('notification/new.html.twig', [
            'notification' => $notification,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_notification_show', methods: ['GET'])]
    public function show(Notification $notification): Response
    {
        return $this->render('notification/show.html.twig', [
            'notification' => $notification,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_notification_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Notification $notification, NotificationRepository $notificationRepository, FormError $formError): Response
    {

        $form = $this->createForm(NotificationType::class, $notification, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_notification_edit', [
                    'id' =>  $notification->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_notification_index');


            if ($form->isValid()) {

                $notificationRepository->save($notification, true);
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

        return $this->renderForm('notification/edit.html.twig', [
            'notification' => $notification,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_notification_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Notification $notification, NotificationRepository $notificationRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'app_notification_delete'
                ,   [
                        'id' => $notification->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $notificationRepository->remove($notification, true);

            $redirect = $this->generateUrl('app_notification_index');

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

        return $this->renderForm('notification/delete.html.twig', [
            'notification' => $notification,
            'form' => $form,
        ]);
    }
}
