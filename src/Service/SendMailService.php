<?php

namespace App\Service;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

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

        $transport = Transport::fromDsn('smtp://maildev_user:maildev_pass@127.0.0.1:2125');
        $mailer = new Mailer($transport);

        $this->mailer = $mailer;
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
            $email = (new Email())
                ->from('hello@example.com')
                ->to('you@example.com')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Time for Symfony Mailer!')
                ->text('Sending emails is fun again!')
                ->html('<p>See Twig integration for better HTML integration!</p>');

            $this->mailer->send($email);

            // Create the mail
//            $email = (new TemplatedEmail())
//                ->from($from)
//                ->to($to)
//                ->subject($subject)
//                ->htmlTemplate("security/$template.html.twig")
//                ->context($context);

            $this->logger->info('The email is about to be sent');
            // Send the mail
            //$this->mailer->send($email);

            // Share the log message
            $this->logger->info('Email sent successfully');
        } catch (TransportExceptionInterface $e) {
            // Log the error
            $this->logger->error('An error occurred while trying to send the email: ' . $e->getMessage());
        }
    }
}
