<?php
namespace App\Controller;

use App\Document\Subscriber;
use App\Message\SendEmailMessage;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class SubscriberApiController extends AbstractController
{
    #[Route('/api/subscribe', methods: ['POST'])]
    public function subscribe(Request $request,
                              DocumentManager $dm,
                              SerializerInterface $serializer,
                              MessageBusInterface $bus,
                              ValidatorInterface $validator): JsonResponse
    {
        $subscriberData = $request->getContent();
        /** @var Subscriber $subscriber */
        $subscriber = $serializer->deserialize($subscriberData, Subscriber::class, 'json');
        $errors = $validator->validate($subscriber);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $dm->persist($subscriber);
        $dm->flush();
        // Essayer d'envoyer l'email
        try {
            $bus->dispatch(new SendEmailMessage($subscriber->getEmail(), $subscriber->getNom(), $subscriber->getCanal()));
            return new JsonResponse([
                'message' => 'Inscription réussie, e-mail envoyé.',
            ], Response::HTTP_CREATED);
        } catch (TransportExceptionInterface $e) {
            // Gérer l'erreur d'envoi d'email
            return new JsonResponse([
                'message' => 'Inscription réussie, mais l\'e-mail n\'a pas pu être envoyé.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
