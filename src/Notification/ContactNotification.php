<?php

namespace App\Notification;

use App\Entity\Contact;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ContactNotification
{

    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $renderer;

    public function __construct(MailerInterface $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;

    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function notify(Contact $contact)
    {
        $email = (new Email())
            ->from('augusthihea@gmail.com')
            ->to('contact@gmail.com')
            ->replyTo($contact->getEmail())
            ->subject('Je suis intéressé par votre Bien!')
            ->text('Sending emails is fun again!')
            ->html($this->renderer->render('emails/contact.html.twig', [
                'contact' => $contact,
            ]), 'text/html');


        $this->mailer->send($email);

    }

}