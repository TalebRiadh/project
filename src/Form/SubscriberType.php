<?php

namespace App\Form;

use App\Document\Subscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class SubscriberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $canal = $options['canal'];
        $builder
            ->add('full_name', TextType::class, [
                'label' => 'Nom & prénom *',
                'attr' => [
                    'placeholder' => 'Votre nom & prénom',
                    'id' => 'full-name'
                ],
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail *',
                'attr' => [
                    'placeholder' => 'Votre adresse e-mail',
                    'class' => 'input',
                    'id' => 'email'
                ],
                'required' => true,
            ])
            ->add('phone', TelType::class, [
                'label' => 'Numéro de téléphone *',
                'attr' => [
                    'placeholder' => 'Votre numéro de téléphone',
                    'class' => 'input',
                    'id' => 'phone'
                ],
                'required' => true,
            ])
            ->add('company', TextType::class, [
                'label' => 'Entreprise *',
                'attr' => [
                    'placeholder' => 'Votre entreprise',
                    'class' => 'input',
                    'id' => 'company'
                ],
                'required' => true,
            ])
            ->add('consent', CheckboxType::class, [
                'label' => 'En cochant cette case, vous acceptez que vos données soient traitées par Prizy et d’être recontacté. Vous pouvez consulter notre Politique de confidentialité',
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'style' => 'accent-color: #e39dca;',
                    'id' => 'consent'
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Demander une démo',
                'attr' => [
                    'class' => 'default-button w-100'
                ],
            ]);
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($canal) {
            $event->getData()->setCanal($canal);
        });
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscriber::class,
            'canal' => null
        ]);
    }
}