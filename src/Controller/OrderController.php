<?php

namespace App\Controller;

use App\Controller\Service\CartService;
use App\Form\OrderType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
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

    // On créer une fonction pour supprimer la commande
    #[Route('/commande/supprimer', name: 'order.delete', methods: ['GET'])]
    public function deleteOrder(
        CartService $cartService,
    ): Response {
        $cartService->deleteAll();

        return $this->redirectToRoute('index');
    }
}
