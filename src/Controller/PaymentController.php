<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\OrderDetails;
use Stripe\Checkout\Session;
use App\Entity\Product\Product;
use App\Controller\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
      private EntityManagerInterface $em;

      private UrlGeneratorInterface $generator;

      public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $generator)
      {
            $this->em = $em;
            $this->generator = $generator;
      }

      #[Route('/commande/create-session-stripe/{reference}', name: 'payment.stripe', methods: ['GET', 'POST'])]
      public function stripeCheckout($reference): RedirectResponse
      {

            $productStripe = [];

            $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);

            if (!$order) {
                  return $this->redirectToRoute('cart');
            }

            foreach ($order->getOrderDetails()->getValues() as $product) {
                  $productData = $this->em->getRepository(Product::class)->findOneBy(['title' => $product->getProduct()]);

                  $productStripe[] = [
                        'price_data' => [
                              'currency' => 'eur',
                              'unit_amount' => $productData->getPrice(),
                              'product_data' => [
                                    'name' => $product->getProduct(),
                              ],
                        ],
                        'quantity' => $product->getQuantity(),
                  ];
            }

            $productStripe[] = [
                  'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => $order->getTransporterPrice(),
                        'product_data' => [
                              'name' => $order->getTransporterName(),
                        ],
                  ],
                  'quantity' => 1,
            ];

            Stripe::setApiKey($_ENV['STRIPE_SECRET']);

            $checkout_session = Session::create([
                  'customer_email' => $this->getUser()->getEmail(),
                  'payment_method_types' => ['card'],
                  'line_items' => [[
                        $productStripe
                  ]],
                  'mode' => 'payment',
                  'success_url' => $this->generator->generate(
                        'payment.success',
                        [
                              'reference' => $order->getReference()
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                  ),
                  'cancel_url' => $this->generator->generate(
                        'payment.error',
                        [
                              'reference' => $order->getReference()
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                  ),
            ]);

            $order->setStripeSessionId($checkout_session->id);

            $this->em->flush();

            return new RedirectResponse($checkout_session->url);
      }

      #[Route('/commande/success/{reference}', name: 'payment.success', methods: ['GET', 'POST'])]
      public function stripeSuccess(
            $reference,
            CartService $cartService,
            Service\MailerService $mailerService
      ): Response {
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getEmail()]);
            $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);
            $orderDetails = $this->em->getRepository(OrderDetails::class)->findOneBy(['orderProduct' => $order->getId()]);
            $createdAt = $order->getCreatedAt();
            $product = $this->em->getRepository(Product::class)->findOneBy(['title' => $orderDetails->getProduct()]);
            $transporter = $this->em->getRepository(Product::class)->findOneBy(['title' => $order->getTransporterName()]);

            // Envoie un mail de confirmation de commande
            $mailerService->send(
                  $user->getEmail(),
                  'Votre commande a bien été validée',
                  'order_comfirmation.html.twig',
                  [
                        'order' => $order,
                        'reference' => $reference,
                        'orderDetails' => $orderDetails,
                        'createdAt' => $createdAt->format('d/m/Y à H:i:s'),
                        'product' => $product,
                        'transporter' => $transporter,
                  ]
            );

            // Supprime le panier de la session une fois la commande validée
            $cartService->removeCart();

            // Permet de valider le paiement de la commande sur dans la DB (booléen)
            $order->setIsPaid(true);
            $this->em->flush();

            // Redirige sur une page de confirmation de commande avec récapitulatif de commande
            return $this->render('order/success.html.twig');
      }

      #[Route('/commande/error/{reference}', name: 'payment.error', methods: ['GET', 'POST'])]
      public function stripeError($reference, CartService $cartService): Response
      {
            return $this->render('order/error.html.twig');
      }
}
