<?php

namespace App\Controller;

use App\Entity\User;
use Faker\Core\DateTime;
use App\Form\RegistrationType;
use App\Form\UserPasswordType;
use App\Service\MailerService;
use App\Form\ForgetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

    /**
     * Permet de réinitialiser le mot de passe d'un utilisateur si utilisateur connecté
     *
     * @param EntityManagerInterface $manager
     * @param MailerService $mailerService
     * @param TokenGeneratorInterface $tokenGeneratorInterface
     * @return Response
     */
    #[Route('utilisateur/mot-de-passe-oublie/{id}', name: 'user.edit.password', methods: ['GET', 'POST'])]
    public function editPassword(
        User $user,
        Request $request,
        EntityManagerInterface $manager,
    ): Response {
        // Si l'utilisateur n'est pas connecté
        if (!$this->getUser()) {
            $this->addFlash(
                'danger',
                'Vous devez être connecté pour modifier votre mot de passe.'
            );
            return $this->redirectToRoute('security.login');
        }

        // On vérifie que l'utilisateur connecté est bien l'utilisateur à éditer
        if ($this->getUser() !== $user) {
            $this->addFlash(
                'danger',
                'Vous devez être connecté pour modifier votre mot de passe.'
            );
            return $this->redirectToRoute('security.login');
        }

        // On crée le formulaire d'édition des informations de l'utilisateur
        $form = $this->createForm(UserPasswordType::class);

        // On traite la soumission du formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            // On met à jour le mot de passe de l'utilisateur dans la base de données
            $manager->flush();

            // On ajoute un message flash pour indiquer que la modification a réussi
            $this->addFlash(
                'success',
                'Votre mot de passe a bien été modifié.'
            );

            // On redirige l'utilisateur vers son profil
            return $this->redirectToRoute('user.index');
        }

        return $this->render('pages/users/edit_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet de la réinitialisation du mot de passe d'un utilisateur si mot de passe oublié
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Service\MailerService $mailerService
     * @param TokenGeneratorInterface $tokenGeneratorInterface
     * @return Response
     */
    #[Route('/mot-de-passe-oublie', name: 'forget.password', methods: ['GET', 'POST'])]
    public function forgetPassword(
        Request $request,
        EntityManagerInterface $manager,
        Service\MailerService $mailerService,
        TokenGeneratorInterface $tokenGeneratorInterface
    ): Response {

        $form = $this->createForm(ForgetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // je souhaite récupérer l'email de l'utilisateur en fonction de l'email rentré dans le formulaire
            $user = $form->getData();

            // Je souhaite savoir si cette adresse email est enregistré dans ma base de données
            $user = $manager->getRepository(User::class)->findOneBy(['email' => $user]);

            // Génération d'un token du mot de passe
            $tokenPassword = $tokenGeneratorInterface->generateToken();

            // On enregistre un token pour le mot de passe à l'utilisateur
            $user->setTokenPassword($tokenPassword);

            // Création du mail de confirmation de réinitialisation du mot de passe
            $mailerService->send(
                $user->getEmail(),
                'Confirmation de réinitialisation de votre mot de passe',
                'password_reset_confirmation.html.twig',
                [
                    'user' => $user,
                    'token' => $tokenPassword,
                    'lifeTimeToken' => $user->getTokenPasswordLifeTime()->format('d/m/Y à H:i:s')
                ]
            );

            // Message flash de confirmation de la réinitialisation du mot de passe
            $this->addFlash(
                'success',
                'Votre demande a bien été prise en compte. Veuillez confirmer votre demande de réinitialisation de mot de passe en cliquant sur le lien que vous avez reçu par email.'
            );

            // Insertion dans la base de données
            $manager->persist($user);
            $manager->flush();

            // Redirection vers la page de connexion
            return $this->redirectToRoute('forget.password');
        }

        return $this->render('pages/users/forget_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet de confirmer la réinitialisation du mot de passe d'un utilisateur
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @param string $token
     * @return Response
     */
    #[Route('/mot-de-passe-oublie/confirmation/{token}', name: 'security.password_reset_confirmation', methods: ['GET', 'POST'])]
    public function passwordResetConfirmation(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher,
        string $token,
    ): Response {
        $user = $manager->getRepository(User::class)->findOneBy(['tokenPassword' => $token]);

        // Si le token est différent de celui attribué à l'utilisateur
        if ($token !== $user->getTokenPassword()) {
            // Retourne un message d'erreur si le token est différent de celui attribué à l'utilisateur
            throw new AccessDeniedException("Le token de confirmation n'est pas valide.");
        }

        // Si le token est expiré
        if (new DateTime('now') > $user->getTokenPasswordLifeTime()) {
            // Retourne un message d'erreur si le token est expiré
            throw new AccessDeniedException("Le token de confirmation a expiré. Veuillez vous réinscrire.");
        }

        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->getData()['plainPassword'])) {
                $user->setUpdatedAt(new \DateTimeImmutable());
                $user->setPlainPassword(
                    $form->getData()['newPassword']
                );

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Le mot de passe a été modifié avec succès. Vous pouvez maintenant vous connecter.'
                );

                return $this->redirectToRoute('user.index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe est incorrect.'
                );
            }
        }

        return $this->render('pages/users/edit_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
