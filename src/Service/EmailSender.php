<?php
namespace App\Service;

use App\Document\Subscriber;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailSender
{
    private $mailer;
    private $params;

    public function __construct(MailerInterface $mailer, ParameterBagInterface $params)
    {
        $this->mailer = $mailer;
        $this->params = $params;
    }

    public function sendEmailWithAttachment(Subscriber $to)
    {
        $staticFilePath = $this->params->get('kernel.project_dir') . '/public/files/';
        $fileUrlBase = $this->params->get('APP_URL') . '/files/';
        // Parcourir tous les fichiers dans le répertoire
        $files = scandir($staticFilePath);
        if ($files === false) {
            throw new FileNotFoundException('Le répertoire ' . $staticFilePath . ' ne peut pas être lu.');
        }

        // Envoyer un e-mail à l'abonné avec un fichier joint
        $email = (new Email())
            ->from('your_email@example.com')
            ->to($to->getEmail())
            ->subject('Bienvenue');
        $htmlBody = 'Bonjour ' . $to->getName() . ', merci de vous être abonné.<br>Voici vos fichiers de bienvenue :<br>';

        foreach ($files as $file) {
            // Ignorer les répertoires et les fichiers non lisibles
            if (is_file($staticFilePath . $file) && is_readable($staticFilePath . $file)) {
                // Ajouter le fichier en tant que pièce jointe
                $email->attachFromPath($staticFilePath . $file, $file);
                // Ajouter un lien vers le fichier dans le corps de l'e-mail
                $fileUrl = $fileUrlBase . $file;
                $htmlBody .= '<a href="' . $fileUrl . '">' . $file . '</a><br>';
            }
        }
        $email->html($htmlBody);
        $this->mailer->send($email);
        }
}
