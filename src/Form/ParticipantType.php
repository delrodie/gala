<?php

namespace App\Form;

use App\Entity\Participant;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,[
                'attr'=>['class'=>'form-control','placeholder'=>"participant.placeholderNom",'autocomplete'=>"off"],
                'label'=>"participant.labelNom"
            ])
            ->add('prenoms', TextType::class,[
                'attr'=>['class'=>'form-control','placeholder'=>"participant.placeholderPrenoms",'autocomplete'=>"off"],
                'label'=>"participant.labelPrenoms"
            ])
            ->add('ville', TextType::class,[
                'attr'=>['class'=>'form-control','placeholder'=>"participant.placeholderVille",'autocomplete'=>"off"],
                'label'=>"participant.labelVille"
            ])
            ->add('telephone', TelType::class,[
                'attr'=>['class'=>'form-control','placeholder'=>"participant.placeholderTelephone","autocomplete"=>"off"],
                'label'=>"participant.labelTelephone"
            ])
            ->add('email',EmailType::class,[
                'attr'=>['class'=>'form-control','placeholder'=>"participant.placeholderEmail",'autocomplete'=>"off"],
                'label'=>"participant.labelEmail"
            ])
            ->add('montant', IntegerType::class,[
                'attr'=>['class'=>"form-control",'placeholder'=>"participant.placeholderMontant",'autocomplete'=>"off"],
                'label'=>"participant.labelMontant"
            ])
            ->add('nombreTicket', IntegerType::class,[
                'attr'=>['class'=>"form-control",'placeholder'=>'participant.placeholderNombre',"autocomplete"=>"off"],
                'label'=>"participant.labelNombre"
            ])
            ->add('montantTotal', IntegerType::class,[
                'attr'=>['class'=>'form-control','placeholder'=>"participant.placeholderTotal",'autocomplete'=>"off"],
                'label'=>'participant.labelTotal'
            ])
            //->add('slug')
            //->add('code')
            //->add('createdAt')
            ->add('club', EntityType::class,[
                'attr'=>['class'=>'form-control select2', 'style'=>"width:100%"],
                'class'=> 'App:Club',
                'query_builder'=> function(EntityRepository $er){
                    return $er->liste();
                },
                'choice_label' =>'nom',
                'label'=>'participant.labelClub',
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
