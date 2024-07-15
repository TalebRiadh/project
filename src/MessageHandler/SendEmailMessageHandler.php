<?php
namespace App\MessageHandler;

use App\Enum\CanalType;
use App\Message\SendEmailMessage;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
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

    public function __invoke(SendEmailMessage $message)
    {
        $this->logger->info('Starting email send process');

        // Chemin vers le répertoire des fichiers statiques
        $staticFilePath = $this->params->get('kernel.project_dir') . '/public/files/';
        $fileUrlBase = $this->params->get('APP_URL') . '/files/';

        // Parcourir tous les fichiers dans le répertoire
        $files = scandir($staticFilePath);
        if ($files === false) {
            $this->logger->error('Cannot read directory: ' . $staticFilePath);
            throw new Exception('Le répertoire ' . $staticFilePath . ' ne peut pas être lu.');
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

        if ($message->getCanal() == CanalType::EMAIL){
            $template = 'mails/canal1.html.twig';
        }else{
            $template = 'mails/canal2.html.twig';
        }

        $email = (new TemplatedEmail())
            ->from(new Address('your_email@example.com', 'Your Name'))
            ->to(new Address($message->getEmail()))
            ->subject('Bienvenue')
            ->htmlTemplate($template)
            ->context([
                'name' => $message->getNom(),
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
