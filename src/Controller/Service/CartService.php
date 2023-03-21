<?php

namespace App\Controller\Service;

use App\Entity\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
      private RequestStack $requestStack;
      private EntityManagerInterface $entityManager;

      public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
      {
            $this->requestStack = $requestStack;
            $this->entityManager = $entityManager;
      }

      // Ajout au panier
      public function add(int $id): void
      {
            $session = $this->getSession();
            $cart = $session->get('cart', []);
            if (!array_key_exists($id, $cart)) {
                  $cart[$id] = 0;
            }
            $cart[$id]++;
            $session->set('cart', $cart);
      }

      // Récupération du total du panier
      public function getTotal(): array
      {
            $cart = $this->getSession()->get('cart', []);
            $cartData = [];
            foreach ($cart as $id => $quantity) {
                  $cartData[] = [
                        'product' => $this->entityManager->getRepository(Product::class)->find($id),
                        'quantity' => $quantity
                  ];
            }
            
            return $cartData;
      }
      
      // Décrémentation du produit du panier
      public function decrement(int $id): void
      {
            $session = $this->getSession();
            $cart = $session->get('cart', []);
            if (array_key_exists($id, $cart)) {
                  if ($cart[$id] > 1) {
                        $cart[$id]--;
                  } else {
                        unset($cart[$id]);
                  }
                  $session->set('cart', $cart);
            }
      }

      // Suppresion d'un article du panier
      public function remove(int $id): void
      {
            $session = $this->getSession();
            $cart = $session->get('cart', []);
            if (array_key_exists($id, $cart)) {
                  unset($cart[$id]);
                  $session->set('cart', $cart);
            }
      }

      // Suppression du panier
      public function deleteAll()
      {
            return $this->getSession()->remove('cart');
      }

      // Récupération de la session
      private function getSession(): ?SessionInterface
      {
            return $this->requestStack->getSession();
      }
}
