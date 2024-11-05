<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Place;
use App\Entity\State;
use App\Entity\Trip;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null,[
                'label' => 'Nom de la sortie :',
                'required' => true
            ])
            ->add('startDateTime', DateTimeType::class, [
                'widget' => 'single_text', // Affiche un champ unique avec le sélecteur de date et heure
                'label' => 'Date et heure de la sortie :',
                'required' => true,
            ])
            ->add('registrationDeadline', DateTimeType::class, [
                'required' => true,
                'widget' => 'single_text',
                'label' => "Date limite de d'inscription :"
            ])
            ->add('nbRegistrationMax', null, [
                'required' => true,
                'label' => 'Nombre de places'
            ])
            ->add('duration', null, [
                'required' => true,
                'data' => 90,
                'attr' => [
                    'min' => 15,
                    'max' => 240,
                    'step' => 15,
                ],
                'label' => 'Durée',
            ])
            ->add('info', null, [
                'label' => 'Description et infos :',
                'required' => true,
                'attr' => [
                    'rows' => 15,
                    'placeholder' => 'Entrez votre description ici...',
                ],
            ])
            ->add('relativeCampus', EntityType::class, [
                'required' => true,
                'class' => Campus::class,
                'choice_label' => 'name',
            ])

            ->add('place', EntityType::class, [
                'required' => true,
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
