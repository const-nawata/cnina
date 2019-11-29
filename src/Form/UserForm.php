<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

//use Symfony\Contracts\Translation\TranslatorInterface;

class UserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options )
    {

    	$pass_reqrd	= $options['attr']['mode'] == 'register';

        $builder
			->add('username', TextType::class, ['attr' => ['class'=> 'form-control'], 'required' => true ] )

            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
				'attr' => ['class'=> 'form-control'], 'required' => $pass_reqrd
            ])

			->add('firstname', TextType::class, ['attr' => ['class'=> 'form-control'], 'required' => false ] )
			->add('surname', TextType::class, ['attr' => ['class'=> 'form-control'], 'required' => false ] )
			->add('postcode', TextType::class, ['attr' => ['class'=> 'form-control'], 'required' => false ] )
			->add('address', TextType::class, ['attr' => ['class'=> 'form-control'], 'required' => false ] )
			->add('phone', TextType::class, ['attr' => ['class'=> 'form-control'], 'required' => false ] )
			->add('mailAddr', EmailType::class, ['attr' => ['class'=> 'form-control'], 'label' => 'Адрес e-mail', 'required' => false ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
