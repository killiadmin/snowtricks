<?php

namespace App\Service;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendMailService
{
    private MailerInterface $mailer;
    private Logger $logger;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;

        // create a log channel
        $this->logger = new Logger('mail');
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/mail.log', Logger::DEBUG));
    }

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

            $this->logger->info('The email is about to be sent');
            // Send the mail
            $this->mailer->send($email);

            // Share the log message
            $this->logger->info('Email sent successfully');
        } catch (TransportExceptionInterface $e) {
            // Log the error
            $this->logger->error('An error occurred while trying to send the email: ' . $e->getMessage());
        }
    }
}
