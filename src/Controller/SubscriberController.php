<?php
namespace App\Controller;

use App\Document\Subscriber;
use App\Form\SubscriberType;
use App\Message\SendEmailMessage;
use App\Service\TemplateEmail;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubscriberController extends AbstractController
{
    #[Route('/', name: 'outbound')]
    public function Outbound(Request $request, DocumentManager $dm, MessageBusInterface $bus, ValidatorInterface $validator): Response
    {
        $subscriber = new Subscriber();
        $form = $this->createForm(SubscriberType::class, $subscriber, ['canal' => 'out']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dm->persist($subscriber);
            $dm->flush();
            try {
                $bus->dispatch(new SendEmailMessage($subscriber->getEmail(), $subscriber->getFullname(), $subscriber->getCanal()));
            } catch (ExceptionInterface $e) {
                $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email');
                return $this->redirectToRoute('outbound');
            }
            $this->addFlash('success', 'Inscription réussie');
            return $this->redirectToRoute('outbound');
        }

        return $this->render('subscription/page.html.twig', [
                'form' => $form->createView(),
            ]);
    }
}
