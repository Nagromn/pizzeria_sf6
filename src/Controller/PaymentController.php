<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
      private EntityManagerInterface $em;

      public function __construct(EntityManagerInterface $em)
      {
            $this->em = $em;
      }

      #[Route('/commande/paiement-stripe', name: 'payment.stripe', methods: ['GET', 'POST'])]
      public function stripeCheckout(mixed $reference): RedirectResponse
      {
           $order = $this->em->getRepository(Order::class)->findOneBy('reference', $reference);
      }
}
