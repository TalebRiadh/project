<?php
namespace App\MessageHandler;

use App\Enum\CanalType;
use App\Message\SendEmailMessage;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsMessageHandler]
class SendEmailMessageHandler
{
    private MailerInterface $mailer;
    private ParameterBagInterface $params;

    public function __construct(MailerInterface $mailer, ParameterBagInterface $params)
    {
        $this->mailer = $mailer;
        $this->params = $params;
    }

    public function __invoke(SendEmailMessage $message)
    {
        // Chemin vers le répertoire des fichiers statiques
        $staticFilePath = $this->params->get('kernel.project_dir') . '/public/files/';
        $fileUrlBase = $this->params->get('APP_URL') . '/files/';

        // Parcourir tous les fichiers dans le répertoire
        $files = scandir($staticFilePath);
        if ($files === false) {
            throw new Exception('Le répertoire ' . $staticFilePath . ' ne peut pas être lu.');
        }

        $fileData = [];
        foreach ($files as $file) {
            // Ignorer les répertoires et les fichiers non lisibles
            if (is_file($staticFilePath . $file) && is_readable($staticFilePath . $file)) {
                // Ajouter le fichier aux données à passer au template
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
        // Création de l'e-mail
        $email = (new TemplatedEmail())
            ->from(new Address('your_email@example.com', 'Your Name'))
            ->to(new Address($message->getEmail()))
            ->subject('Bienvenue')
            ->htmlTemplate($template)
            ->context([
                'name' => $message->getNom(),
                'files' => $fileData,
            ]);

        // Ajouter les pièces jointes
        foreach ($fileData as $file) {
            $email->attachFromPath($staticFilePath . $file['name'], $file['name']);
        }

        // Envoyer l'e-mail
        $this->mailer->send($email);
    }
}
