<?php

namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Twig\Environment;

class SendMailService
{
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;

        $transport = Transport::fromDsn('smtp://maildev_user:maildev_pass@127.0.0.1:2125');
        $mailer = new Mailer($transport);
        $this->mailer = $mailer;
    }

    /**
     * Sends an email using the provided parameters.
     *
     * @param string $from The email address sending the email.
     * @param string $to The email address receiving the email.
     * @param string $subject The subject of the email.
     * @param string $template The name of the email template to use.
     * @param array $context An array of variables to pass to the email template.
     *
     * @throws \RuntimeException If an error occurs while trying to send the email.
     */
    public function sendMail(
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
                ->html($this->twig->render("security/$template.html.twig", $context));

            // Send the mail
            $this->mailer->send($email);

            // Share the log message
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('An error occurred while trying to send the email: ' . $e->getMessage(), 0, $e);
        }
    }
}
