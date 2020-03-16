<?php

namespace App\Form;

use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', TextType::class,[
                'attr'=>['class'=>'form-control','autocomplete'=>"off", 'readonly'=>'readonly'],
                'label' => 'invite.labelReference'
            ])
            ->add('invite', TextType::class,[
                'attr'=>['class'=>'form-control','placeholder'=>'invite.placeholderInvite','autocomplete'=>"off"],
                'label' => 'invite.labelInvite'
            ])
            ->add('invitePhone', TextType::class,[
                'attr'=>['class'=>'form-control bfh-phone','placeholder'=>'invite.placeholderPhone','autocomplete'=>"off", 'data-format'=>"+225 dddddddd"],
                'label' => 'invite.labelPhone',
                'required' => true
            ])
            /*   'attr'=>['class'=>'form-control','placeholder'=>'invite.placeholderMail','autocomplete'=>"off"],
                'label'=> 'invite.labelMail',
                'required' => false
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
