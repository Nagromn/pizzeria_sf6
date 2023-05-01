<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use App\Controller\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

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
    public function verifyOrder(CartService $cartService, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        $form = $this->createForm(OrderType::class, [
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        // dd($form->getData());

        if ($form->isSubmitted() && $form->isValid()) {
            $datetime = new \DateTime('now');
            $transporter = $form->get('transporter')->getData();
            $delivery = $form->get('address')->getData();
            $deliveryForOrder = $delivery->getFullName();
            $deliveryForOrder .= '</br>' . $delivery->getAddress();
            $deliveryForOrder .= '</br>' . $delivery->getZipcode() . ' ' . $delivery->getCity();
            $reference = $datetime->format('dmY') . '-' . uniqid();
            $order = new Order();
            $order->setUser($this->getUser())
                ->setReference($reference)
                ->setCreatedAt($datetime)
                ->setDelivery($deliveryForOrder)
                ->setTransporterName($transporter->getTitle())
                ->setTransporterPrice($transporter->getPrice())
                ->setIsPaid(false)
                ->setMethod('stripe');

            $this->em->persist($order);

            foreach ($cartService->getTotal() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setOrderProduct($order)
                    ->setQuantity($product['quantity'])
                    ->setProduct($product['product']->getTitle())
                    ->setPrice($product['product']->getPrice())
                    ->setProduct($product['product']->getTitle())
                    ->setTotalOrder($product['product'])->getPrice() * $product['quantity'];

                $this->em->persist($orderDetails);
            }

            $this->em->flush();

            return $this->render('order/recap.html.twig', [
                'method' => $order->getMethod(),
                'recapCart' => $cartService->getTotal(),
                'transporter' => $transporter,
                'delivery' => $deliveryForOrder,
                'reference' => $order->getReference(),
            ]);
        }

        return $this->redirectToRoute('order.verify');
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
        $cartService->removeCart();

        return $this->redirectToRoute('index');
    }
}
