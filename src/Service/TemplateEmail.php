<?php
namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Psr\Log\LoggerInterface;

class TemplateEmail
{


    public function __construct(private readonly ParameterBagInterface $params, private readonly MailerInterface $mailer, private readonly LoggerInterface $logger){}

    public function createEmail(string $email, string $prenom, string $canal): TemplatedEmail
    {
        $staticFilePath = null;
        $fileData = null;

        if ($canal === 'in') {
            $staticFilePath = $this->params->get('kernel.project_dir') . '/public/files/guide.pdf';
            $fileUrlBase = $this->params->get('APP_URL') . '/files/guide.pdf';

            if (is_file($staticFilePath) && is_readable($staticFilePath)) {
                $fileData = [
                    'name' => 'guide.pdf',
                    'url' => $fileUrlBase
                ];
            } else {
                $this->logger->error('The file sample.pdf is not readable or does not exist: ' . $staticFilePath);
                throw new NotFoundHttpException('Le fichier guide.pdf ne peut pas être lu ou n\'existe pas dans le répertoire ' . $staticFilePath . '.');
            }
        }

        $template = $canal === 'in' ? 'mails/in_template.html.twig' : 'mails/out_template.html.twig';
        $email = (new TemplatedEmail())
            ->from(new Address('contact@prizy.co', 'Prizy.co'))
            ->to(new Address($email))
            ->subject('no-reply')
            ->htmlTemplate($template)
            ->context([
                'prenom' => $prenom,
                'canal' => $canal,
                'file' => $fileData,
            ]);

        if ($fileData !== null) {
            $email->attachFromPath($staticFilePath, 'guide.pdf');
        }
        return $email;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmail(TemplatedEmail $email): void
    {
        try{
            $this->mailer->send($email);
        }catch (TransportExceptionInterface $e){
            $this->logger->error('Error sending email: ' . $e->getMessage());
            throw $e;
        }
    }
}
