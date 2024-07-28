<?php
namespace App\Controller;

use App\Document\Subscriber;
use App\Form\SubscriberType;
use App\Message\SendEmailMessage;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class SubscriberController extends AbstractController
{
    #[Route('/subscribe/{canal}', name: 'subscriber')]
    public function subscribe(Request $request, DocumentManager $dm, MessageBusInterface $bus, string $canal): Response
    {
        $subscriber = new Subscriber();
        $form = $this->createForm(SubscriberType::class, $subscriber, ['canal' => $canal]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dm->persist($subscriber);
            $dm->flush();

            try {
                $bus->dispatch(new SendEmailMessage($subscriber->getEmail(), $subscriber->getPrenom(), $subscriber->getCanal()));
            } catch (ExceptionInterface $e) {
                $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email');
            }

            $this->addFlash('success', 'Inscription réussie');
            return $this->redirectToRoute('subscriber', ['canal' => $canal]);
        }

        return $this->render('subscription/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
