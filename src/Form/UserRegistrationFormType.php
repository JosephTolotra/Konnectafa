<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('fullname')
            ->add('email')
            ->add('plainPassword',RepeatedType::class,[
                'mapped'=>false,
                'type'=>PasswordType::class,
                'invalid_message'=>'Mila mitovy ilay motpasse',
                'required'=> true,
                'first_options'=>['label'=>'Password'],
                'second_options'=>['label'=>'Comfirm Password'],
                'constraints'=> [
                    new NotBlank,
                    new Length(['min'=> 6, 'max'=> 4096])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
