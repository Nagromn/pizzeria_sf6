<?php

namespace App\Controller;

use App\Controller\Service\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    #[Route('/panier', name: 'cart', methods: ['GET'])]
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/cart.html.twig', [
            'cart' => $cartService->getTotal(),
        ]);
    }

    // Ajout au panier
    #[Route('/panier/add/{id<\d+>}', name: 'cart.add', methods: ['GET'])]
    public function add(
        CartService $cartService,
        int $id
    ): Response {
        $cartService->add($id);

        return $this->redirectToRoute('menu');
    }

    // Incrémentation d'un article du panier
    #[Route('/panier/increment/{id<\d+>}', name: 'cart.increment', methods: ['GET'])]
    public function increment(
        CartService $cartService,
        int $id
    ): Response {
        $cartService->increment($id);

        return $this->redirectToRoute('cart');
    }

    // Décrementation d'un article du panier
    #[Route('/panier/decrement/{id<\d+>}', name: 'cart.decrement', methods: ['GET'])]
    public function decrement(
        CartService $cartService,
        int $id
    ): Response {
        $cartService->decrement($id);

        return $this->redirectToRoute('cart');
    }

    // Suppression de tout le panier
    #[Route('/panier/remove', name: 'cart.remove', methods: ['GET'])]
    public function removeCart(CartService $cartService): Response
    {
        $cartService->removeCart();

        return $this->redirectToRoute('cart');
    }
    

}
