<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class SendMailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(
        string $from,
        string $to,
        string $subject,
        string $template,
        array  $context
    ): void
    {
        try {
            // Create the mail
            $email = (new TemplatedEmail())
                ->from($from)
                ->to($to)
                ->subject($subject)
                ->htmlTemplate("security/$template.html.twig")
                ->context($context);

            // Send the mail
            $this->mailer->send($email);
            error_log('Email sent successfully');
        } catch (TransportExceptionInterface $e) {
            // Log the error
            error_log('An error occurred while trying to send the email: ' . $e->getMessage());
        }
    }
}
