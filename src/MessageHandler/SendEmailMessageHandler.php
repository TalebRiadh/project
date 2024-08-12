<?php
// src/MessageHandler/SendEmailMessageHandler.php

namespace App\MessageHandler;

use App\Message\SendEmailMessage;
use App\Service\TemplateEmail;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendEmailMessageHandler
{

    public function __construct(private readonly TemplateEmail $templateEmail, private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(SendEmailMessage $message): void
    {
        $this->logger->info('Starting email send process');

        try {
            $email = $this->templateEmail->createEmail($message->getEmail(), $message->getFullname(), $message->getCanal());

            $this->logger->info('Sending email to ' . $message->getEmail());
            $this->templateEmail->sendEmail($email);
            $this->logger->info('Email sent successfully');
        } catch (\Exception $e) {
            $this->logger->error('Error sending email: ' . $e->getMessage());
            throw $e;
        }
    }
}

