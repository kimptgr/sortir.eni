<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Trip;
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
/*            ->add('roles', Array_::class, [

            ])*/
            -> add('password', RepeatedType::class, array(
                'required' => true,
                'type' => PasswordType::class,
                'first_options' => array('label' => 'label.password'),
                'second_options' => array('label' => 'label.confirm_password'),
                ))
            ->add('lastName')
            ->add('firstName')
            ->add('phoneNumber')

/*            ->add('enrolledTrips', EntityType::class, [
                'class' => Trip::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'id',
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
