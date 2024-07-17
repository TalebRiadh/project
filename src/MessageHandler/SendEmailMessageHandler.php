<?php
namespace App\MessageHandler;

use App\Message\SendEmailMessage;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsMessageHandler]
class SendEmailMessageHandler
{


    public function __construct(private readonly MailerInterface $mailer, private readonly ParameterBagInterface $params, private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(SendEmailMessage $message): void
    {
        $this->logger->info('Starting email send process');

        // Chemin vers le répertoire des fichiers statiques
        $staticFilePath = $this->params->get('kernel.project_dir') . '/public/files/';
        $fileUrlBase = $this->params->get('APP_URL') . '/files/';

        // Parcourir tous les fichiers dans le répertoire
        $files = scandir($staticFilePath);
        if ($files === false) {
            $this->logger->error('Cannot read directory: ' . $staticFilePath);
            throw new FileLocatorFileNotFoundException('Le répertoire ' . $staticFilePath . ' ne peut pas être lu.');
        }

        $fileData = [];
        foreach ($files as $file) {
            if (is_file($staticFilePath . $file) && is_readable($staticFilePath . $file)) {
                $fileData[] = [
                    'name' => $file,
                    'url' => $fileUrlBase . $file
                ];
            }
        }
        $email = (new TemplatedEmail())
            ->from(new Address('your_email@example.com', 'Your Name'))
            ->to(new Address($message->getEmail()))
            ->subject('Bienvenue')
            ->htmlTemplate("mails/template.html.twig")
            ->context([
                'name' => $message->getNom(),
                'canal' => $message->getCanal(),
                'files' => $fileData,
            ]);

        foreach ($fileData as $file) {
            $email->attachFromPath($staticFilePath . $file['name'], $file['name']);
        }

        $this->logger->info('Sending email to ' . $message->getEmail());
        $this->mailer->send($email);
        $this->logger->info('Email sent successfully');
    }

}
