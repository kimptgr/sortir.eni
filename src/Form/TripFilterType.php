<?php

namespace App\Form;

use App\Entity\Campus;
use App\Models\TripFilterModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('relativeCampus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'label' => 'Campus',
                'required' => false,
            ])
            ->add('tripName', TextType::class,[
                'label'    => "Le nom de la sortie contient :",
                'required' => false,])
            ->add('startDateTime', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('registrationDeadline', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('iOrganized', CheckboxType::class, [
                'label'    => "Sorties dont je suis l'organisateur·rice",
                'required' => false,
            ])
            ->add('iParticipate', CheckboxType::class, [
                'label'    => "Sorties auxquelles je suis inscrit·e",
                'required' => false,
            ])
            ->add('imRegistered', CheckboxType::class, [
                'label'    => "Sorties auxquelles je ne suis pas inscrit·e",
                'required' => false,
            ])
            ->add('oldTrips', CheckboxType::class, [
                'label'    => "Sorties passées",
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TripFilterModel::class,
        ]);
    }
}
