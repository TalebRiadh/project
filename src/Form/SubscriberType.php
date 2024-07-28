<?php

namespace App\Form;

use App\Document\Subscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class SubscriberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $canal = $options['canal'];
        $builder
            ->add('nom', TextType::class,[
                'label' => 'Nom',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('prenom', TextType::class,[
                'label' => 'Prénom',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('tel', TextType::class,[
                'label' => 'Téléphone',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('email', EmailType::class,[
                'label' => 'Email',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('souscription', SubmitType::class, [
                'label' => 'Souscription',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ]);
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($canal) {
            $this->onSubmit($event->getData(), $canal);
        });
    }
    private function onSubmit(Subscriber $subscriber, string $canal)
    {
        $subscriber->setCanal($canal);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Subscriber::class,
            'canal' => null
        ]);
    }
}