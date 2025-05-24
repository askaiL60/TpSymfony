<?php

namespace App\Form;

use App\Entity\Preferences;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreferencesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('theme', ChoiceType::class, [
                'choices' => [
                    'Clair' => 'light',
                    'Sombre' => 'dark',
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Thème'
            ])
            ->add('notificationsEnabled', CheckboxType::class, [
                'label'    => 'Recevoir les notifications',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Preferences::class,
        ]);
    }
}
