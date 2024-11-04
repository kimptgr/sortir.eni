<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Place;
use App\Entity\State;
use App\Entity\Trip;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('startDateTime', null, [
                'widget' => 'single_text',
            ])
            ->add('duration')
            ->add('registrationDeadline', null, [
                'widget' => 'single_text',
            ])
            ->add('nbRegistrationMax')
            ->add('info')
            ->add('state', EntityType::class, [
                'class' => State::class,
                'choice_label' => 'wording',
            ])
            ->add('relativeCampus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
            ])
            ->add('participants', EntityType::class, [
                'class' => Participant::class,
                'choice_label' => function(Participant $participant) {
                    return $participant->getFirstname() . ' ' . $participant->getLastname();
                },
                'multiple' => true,
            ])

            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => function(Place $place) {
                return $place->getName() . ' ' . $place->getCity()->getName();
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}
