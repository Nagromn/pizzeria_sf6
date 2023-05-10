<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserEmailType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * Permet d'afficher le profil de l'utilisateur
     *
     * @return Response
     */
    #[Route('/utilisateur', name: 'user.index')]
    public function index(): Response
    {
        $user = $this->getUser();
        $orders = $user->getOrders();

        return $this->render('pages/users/profile.html.twig', [
            'user' => $user,
            'orders' => $orders,
        ]);
    }

    /**
     * Permet d'éditer les informations de l'utilisateur
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route('/utilisateur/edition/{id}', name: 'user.edit')]
    public function edit(
        User $user,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher
    ): Response {
        // On vérifie que l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        // On vérifie que l'utilisateur connecté est bien l'utilisateur à éditer
        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('product.index');
        }

        // On crée le formulaire d'édition des informations de l'utilisateur
        $form = $this->createForm(UserType::class, $user);

        // On traite la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())) {

                // On met à jour les données de l'utilisateur
                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();

                // On ajoute un message flash pour indiquer que la modification a réussi
                $this->addFlash(
                    'success',
                    'Les informations de votre compte ont bien été modifiées.'
                );

                // On redirige l'utilisateur vers son profil
                return $this->redirectToRoute('user.index');

                // Si le mot de passe fourni est incorrect
            } else {
                // On ajoute un message flash pour indiquer que le mot de passe fourni est incorrect
                $this->addFlash(
                    'warning',
                    'Le mot de passe est incorrect.'
                );
            }
        }

        // On affiche la page d'édition des informations de l'utilisateur
        return $this->render('pages/users/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet la modification de l'email de l'utilisateur
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route('/utilisateur/edition-email/{id}', 'user.edit.email', methods: ['GET', 'POST'])]
    public function editEmail(
        User $user,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher,
    ): Response {
        // Vérification si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        // Vérification que l'utilisateur connecté est le même que celui que l'on veut modifier
        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('product.index');
        }

        // Création d'un formulaire pour la modification de l'email de l'utilisateur
        $form = $this->createForm(UserEmailType::class);

        // Vérification si le formulaire a été soumis et si les données sont valides
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // Vérification que le mot de passe saisi correspond à celui de l'utilisateur
            if ($hasher->isPasswordValid($user, $form->getData()['plainPassword'])) {
                // Modification de l'email de l'utilisateur et enregistrement en base de données
                $user->setUpdatedAt(new \DateTimeImmutable());
                $user->setEmail(
                    $form->getData()['email']
                );

                $manager->persist($user);
                $manager->flush();

                // Affichage d'un message de succès et redirection vers la page d'accueil
                $this->addFlash(
                    'success',
                    "L'email a été modifié avec succès."
                );

                return $this->redirectToRoute('product.index');
            } else {
                // Affichage d'un message d'erreur si le mot de passe saisi est incorrect
                $this->addFlash(
                    'warning',
                    "L'email est incorrect."
                );
            }
        }

        // Affichage du formulaire de modification de l'email de l'utilisateur
        return $this->render('pages/users/edit_email.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/utilisateur/récapitulatif-de-commande/{id}', 'user.order.recap', methods: ['GET', 'POST'])]
    public function orderRecap(): Response
    {
        // Récupération de l'utilisateur connecté et de ses commandes
        $user = $this->getUser();
        $orders = $user->getOrders();

        // Vérification si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('product.index');
        }

        // Parcours des commandes de l'utilisateur pour récupérer leurs informations
        foreach ($orders as $order) {
            $id = $order->getId();
            $orderDetails = $order->getOrderDetails();
            $reference = $order->getReference();
            $method = $order->getMethod();
            $isPaid = $order->isIsPaid();
            $totalOrder = $order->getTotalOrder();
        }

        return $this->render('pages/users/order_recap.html.twig', [
            'user' => $user,
            'id' => $id,
            'orderDetails' => $orderDetails,
            'reference' => $reference,
            'method' => $method,
            'isPaid' => $isPaid,
            'totalOrder' => $totalOrder,
        ]);
    }
}
