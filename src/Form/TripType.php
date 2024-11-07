<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Participant;
use App\Entity\Place;
use App\Entity\State;
use App\Entity\Trip;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TripType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null,[
                'label' => 'Nom de la sortie :',
                'required' => true
            ])
            ->add('startDateTime', null, [
                'widget' => 'single_text',
                'label' => 'Date et heure de la sortie :',
                'required' => true
            ])
            ->add('registrationDeadline', null, [
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

            ->add('city', EntityType::class, [
                'choice_label' => 'name',
                'mapped' => false,
                'class' => City::class,
                'placeholder' => 'Choose a city',
            ])
            ->add('place', ChoiceType::class, [
                'choices' => [],
                'placeholder' => 'Choose a place',
            ]);
    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}