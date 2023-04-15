<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserConfirmation implements UserCheckerInterface
{
      public function checkPreAuth(UserInterface $user): void
      {
            if (!$user instanceof User) {
                  return;
            }
      }

      public function checkPostAuth(UserInterface $user): void
      {
            if (!$user instanceof User) {
                  return;
            }

            if (!$user->isIsVerified()) {
                  // Le message passé en paramètre sera affiché à l'utilisateur lors de sa tentative de connexion
                  throw new CustomUserMessageAccountStatusException("Votre compte n'est pas encore activé. Veuillez consulter votre boîte mail et confirmer votre compte avant le {$user->getTokenRegistrationLifeTime()->format('d/m/Y à H\hi')}.");
            }
      }
}
