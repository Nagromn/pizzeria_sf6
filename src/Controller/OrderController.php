<?php

namespace App\Controller;

use App\Form\OrderType;
use App\Entity\Transporter;
use App\Controller\Service\CartService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /**
     * Fonction pour afficher le formulaire de commande
     *
     * @param CartService $cartService
     * @return Response
     */
    #[Route('/commande', name: 'order')]
    public function index(CartService $cartService): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        $form = $this->createForm(OrderType::class, [
            'user' => $this->getUser()
        ]);

        return $this->render('order/order.html.twig', [
            'form' => $form->createView(),
            'recapCart' => $cartService->getTotal(),
        ]);
    }

    /**
     * Fonction pour valider la commande
     *
     * @return Response
     */
    #[Route('/commande/validation-de-commande', name: 'order.verify', methods: ['GET', 'POST'])]
    public function verifyOrder(Request $request): Response
    {
        $form = $this->createForm(OrderType::class, [
            'user' => $this->getUser(),
        ]);
        
        $form->handleRequest($request);

        dd($form->getData());

        if($form->isSubmitted() && $form->isValid()) {
           $datetime = new \DateTime('now');
           $transporter = $form->get('user')->getData();
        //    $delivery = $form->get()
        }

        return $this->render('order/recap.html.twig');
    }

    /**
     * Fonction pour supprimer la commande
     *
     * @param CartService $cartService
     * @return Response
     */
    #[Route('/commande/supprimer', name: 'order.delete', methods: ['GET'])]
    public function deleteOrder(
        CartService $cartService,
    ): Response {
        $cartService->deleteAll();

        return $this->redirectToRoute('index');
    }
}
