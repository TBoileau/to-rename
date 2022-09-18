<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ChallengeRule;
use App\Entity\Rule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ChallengeRuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('rule', EntityType::class, [
            'label' => 'Règle',
            'class' => Rule::class,
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) {
            $form = $event->getForm();

            /** @var ?ChallengeRule $challengeRule */
            $challengeRule = $event->getData();

            if (null !== $challengeRule && null !== $challengeRule->getId()) {
                $form->add('hits', IntegerType::class, [
                    'label' => 'Nombre de coups',
                    'attr' => [
                        'min' => 0,
                    ],
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', ChallengeRule::class);
    }
}
