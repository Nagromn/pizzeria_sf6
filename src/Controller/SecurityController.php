<?php

namespace App\Controller;

use App\Entity\User;
use Faker\Core\DateTime;
use App\Form\RegistrationType;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    /**
     * Permet la connexion d'un utilisateur
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route('/connexion', name: 'security.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Render de la page de connexion
        return $this->render('pages/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * Permet la déconnexion de l'utilisateur
     *
     * @return void
     */
    #[Route('/deconnexion', name: 'security.logout')]
    public function logout()
    {
        // Nothing to do here...
    }

    /**
     * Permet l'inscription d'un utilisateur
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param MailerService $mailerService
     * @param TokenGeneratorInterface $tokenGeneratorInterface
     * @return Response
     */
    #[Route('/inscription', name: 'security.registration', methods: ['GET', 'POST'])]
    public function registration(
        Request $request,
        EntityManagerInterface $manager,
        Service\MailerService $mailerService,
        TokenGeneratorInterface $tokenGeneratorInterface
    ): Response {
        $user = new User();

        // On attribue le rôle ROLE_USER à l'utilisateur par défaut
        $user->setRoles(['ROLE_USER']);

        // Création du formulaire d'inscription d'un utilisateur
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            // Génération d'un token
            $tokenRegistration = $tokenGeneratorInterface->generateToken();

            // Récupération des données utilisateurs rentrées dans le $form
            $user = $form->getData();

            // On enregistre un token à l'utilisateur
            $user->setTokenRegistration($tokenRegistration);

            // Création du mail de confirmation d'inscription de l'utilisateur
            $mailerService->send(
                $user->getEmail(),
                'Confirmation d\'inscription du compte utilisateur',
                'registration_confirmation.html.twig',
                [
                    'user' => $user,
                    'token' => $tokenRegistration,
                    'lifeTimeToken' => $user->getTokenRegistrationLifeTime()->format('d/m/Y à H:i:s')
                ]
            );

            // Message flash de confirmation si l'inscription s'est bien déroulée
            $this->addFlash('success', 'Votre compte a bien été crée. Veuillez confirmer votre adresse email en cliquant sur le lien que vous avez reçu par email.');

            // Insertion dans la base de données
            $manager->persist($user);
            $manager->flush();

            // Redirection vers la page de connexion
            return $this->redirectToRoute('security.login');
        }

        // Render de la page inscription
        return $this->render('pages/security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet de confirmer l'inscription d'un utilisateur
     *
     * @param EntityManagerInterface $manager
     * @param string $token
     * @return Response
     */
    #[Route('/inscription/confirmation/{token}', name: 'security.confirmation', methods: ['GET', 'POST'])]
    public function confirmation(
        EntityManagerInterface $manager,
        string $token
    ): Response {
        // Récupération de l'utilisateur en fonction du token
        $user = $manager->getRepository(User::class)->findOneBy(['tokenRegistration' => $token]);

        // Si l'utilisateur n'existe pas
        if (!$user) {
            // Retourne un message d'erreur si l'utilisateur n'existe pas
            throw new AccessDeniedException("L'utilisateur n'existe pas.");
        }

        // Si le token est différent de celui attribué à l'utilisateur
        if ($token !== $user->getTokenRegistration()) {
            // Retourne un message d'erreur si le token est différent de celui attribué à l'utilisateur
            throw new AccessDeniedException("Le token de confirmation n'est pas valide.");
        }

        // Si le token est expiré
        if (new DateTime('now') > $user->getTokenRegistrationLifeTime()) {
            // Retourne un message d'erreur si le token est expiré
            throw new AccessDeniedException("Le token de confirmation a expiré. Veuillez vous réinscrire.");
        }

        // On supprime le token de l'utilisateur
        $user->setTokenRegistration(null);

        // On active le compte de l'utilisateur
        $user->setIsVerified(true);

        // Message flash de confirmation si l'inscription s'est bien déroulée
        $this->addFlash('success', 'Votre compte a bien été activé. Vous pouvez maintenant vous connecter.');

        // Insertion dans la base de données
        $manager->persist($user);
        $manager->flush();

        // Redirection vers la page de connexion
        return $this->redirectToRoute('security.login');
    }
}
