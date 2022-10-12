<?php

declare(strict_types=1);

namespace App\Form;

use App\Doctrine\Entity\Duration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('hours', IntegerType::class, [
                'label' => 'Heures',
                'attr' => [
                    'min' => 0,
                    'max' => 23,
                ],
            ])
            ->add('minutes', IntegerType::class, [
                'label' => 'Minutes',
                'attr' => [
                    'min' => 0,
                    'max' => 59,
                ],
            ])
            ->add('seconds', IntegerType::class, [
                'label' => 'Secondes',
                'attr' => [
                    'min' => 0,
                    'max' => 59,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Duration::class);
    }
}
