<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
//use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options )
    {
//		$widget_css	= 'form-control form-control-sm';

        $builder
			->add('id', IntegerType::class )
		;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
