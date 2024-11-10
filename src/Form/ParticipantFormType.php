<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Trip;
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ParticipantFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder


            ->add('brochure', FileType::class, [
                'label' => 'Moi',



                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using attributes
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF, JPG, or PNG document',
                    ])
                ],
            ]);


        //Ajout d'un listener
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $participant = $event->getData();
            if($participant && $participant->getBrochureFilename()){
                //Cas où on est en modification et qu'une image est déjà présente;
                //On ajoute une checkbox pour permettre de demander la suppression de l'image.
                $form=$event->getForm();
                $form->add('deleteImage',CheckboxType::class,[
                    'required'=>false,
                    'mapped'=>false,
                ]);
            }
        })

            ->add('email')
/*            ->add('roles', Array_::class, [

            ])*/
/*            -> add('password', RepeatedType::class, array(
                'required' => false,
                'type' => PasswordType::class,
                'first_options' => array('label' => 'password'),
                'second_options' => array('label' => 'confirm password'),
                'attr' => ['autocomplete' => 'new-password'],
                'mapped' => false,
                'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a password',
                ]),
                new Length([
                'min' => 6,
                'minMessage' => 'Your password should be at least {{ limit }} characters',
                'max' => 4096,
                ]),
    ],
                ))*/
            ->add('lastName')
            ->add('firstName')
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
            ])
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
