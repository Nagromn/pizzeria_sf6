<?php

namespace App\Controller\Service;

use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

// Service qui permet de générer un email
class MailerService
{
      public function __construct(private readonly MailerInterface $mailer)
      {
      }

      /**
       * @param string $to
       * @param string $subject
       * @param string $teamplateTwig
       * @param array $context
       * @throws TransportExceptionInterface
       */
      public function send(
            string $to,
            string $subject,
            string $teamplateTwig,
            array $context
      ): void {
            $email = (new TemplatedEmail())
                  ->from(new Address('noreply@pizzeria.fr', 'Pizzeria SF 6'))
                  ->to($to)
                  ->subject($subject)
                  ->htmlTemplate("mails/$teamplateTwig")
                  ->context($context);

            try {
                  $this->mailer->send($email);
            } catch (TransportExceptionInterface $transportException) {
                  throw $transportException;
            }
      }
}
