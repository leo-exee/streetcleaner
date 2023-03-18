<?php

namespace App\Form;

use App\Entity\Device;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeviceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('device',TextType::class, [
                'label' => "Identifiant de votre appareil",
                'attr' => array(
                    'disabled' => 'disabled',
                    'placeholder' => '...'
                ),
            ])
            ->add('adress',TextType::class, [
                'label' => "Adresse de votre appareil",
                'attr' => array(
                    'placeholder' => '...'
                ),
            ])
            ->add('temperature', HiddenType::class)
            ->add('humidity', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Device::class,
        ]);
    }
}
