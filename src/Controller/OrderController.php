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

    /**
     * Constructeur de la classe
     *
     * @param EntityManagerInterface $em
     * @return void
     */
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
        // Vérifie si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        // Crée un formulaire pour la commande
        $form = $this->createForm(OrderType::class, [
            'user' => $this->getUser()
        ]);

        // Affiche la page de commande avec le formulaire et un récapitulatif du panier
        return $this->render('order/order.html.twig', [
            'form' => $form->createView(),
            'recapCart' => $cartService->getTotal(),
        ]);
    }

    /**
     * Fonction pour valider la commande
     *
     * @param CartService $cartService
     * @param Request $request
     * @return Response
     */
    #[Route('/commande/validation-de-commande', name: 'order.verify', methods: ['GET', 'POST'])]
    public function verifyOrder(CartService $cartService, Request $request): Response
    {
        // Vérification si l'utilisateur est connecté, sinon redirection vers la page de connexion
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        // Création du formulaire de commande avec l'utilisateur connecté
        $form = $this->createForm(OrderType::class, [
            'user' => $this->getUser(),
        ]);

        // Traitement de la soumission du formulaire
        $form->handleRequest($request);

        // Si le formulaire a été soumis et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Création d'une référence unique pour la commande
            $datetime = new \DateTime('now');
            $transporter = $form->get('transporter')->getData();
            $delivery = $form->get('address')->getData();
            $deliveryForOrder = $delivery->getFullName();
            $deliveryForOrder .= '</br>' . $delivery->getAddress();
            $deliveryForOrder .= '</br>' . $delivery->getZipcode() . ' ' . $delivery->getCity();
            $reference = $datetime->format('dmY') . '-' . uniqid();

            // Création de la commande et persistance en base de données
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

            // Ajout des détails de commande pour chaque produit du panier
            foreach ($cartService->getTotal() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setOrderProduct($order)
                    ->setQuantity($product['quantity'])
                    ->setProduct($product['product']->getTitle())
                    ->setPrice($product['product']->getPrice())
                    ->setTotalProduct($product['product']->getPrice() * $product['quantity']);

                // Calcul du montant total de la commande
                $order->setTotalOrder(($orderDetails->getTotalProduct() * $product['quantity']) + $order->getTransporterPrice());

                // Persistance en base de données
                $this->em->persist($orderDetails);
            }

            // Enregistrement en base de données
            $this->em->flush();

            // Récupération de l'id de la commande de l'utilisateur pour permettre l'annulation de la commande
            $user = $this->getUser();
            $orders = $user->getOrders();

            foreach ($orders as $order) {
                $id = $order->getId();
            }
        }

        // Affichage de la page du récapitulatif de commande
        return $this->render('order/recap.html.twig', [
            'id' => $id,
            'method' => $order->getMethod(),
            'recapCart' => $cartService->getTotal(),
            'transporter' => $transporter,
            'delivery' => $deliveryForOrder,
            'reference' => $order->getReference(),
        ]);
    }

    /**
     * Fonction pour annuler la commande
     *
     * @param Order $order
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('commande/annuler/{id}', 'order.delete', methods: ['GET'])]
    public function delete(Order $order, EntityManagerInterface $manager, Request $request): Response
    {
        // Supprime la commande de l'utilisateur
        $manager->remove($order);

        // Enregistrement en base de données
        $manager->flush();

        // Vide le panier après la suppression de la commande
        $request->getSession()->remove('cart');

        // Affiche un message flash de confirmation de la suppression de la commande
        $this->addFlash(
            'success',
            "Votre commande a été annulée."
        );

        // Redirige vers la page de profil de l'utilisateur
        return $this->redirectToRoute('user.index');
    }
}
