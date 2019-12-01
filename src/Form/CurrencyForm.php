<?php

namespace App\Form;

use App\Entity\Currency;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CurrencyForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options )
    {
		$widget_css	= 'form-control form-control-sm';

        $builder
			->add('id', HiddenType::class, [ 'mapped' => false, 'data' => $options['attr']['currency_id'] ] )
			->add('name', TextType::class, ['attr' => ['class'=> $widget_css], 'required' => false ] )
			->add('symbol', TextType::class, ['attr' => ['class'=> $widget_css], 'required' => false ] )
			->add('ratio', NumberType::class, ['attr' => ['class'=> $widget_css], 'scale' => 6, 'required' => false ] )
			->add('isAfterPos', ChoiceType::class, [
					'choices'	=> [
						'before'	=> false,
						'after'		=> true
					],
					'expanded'	=> true,
					'placeholder' => false,
					'attr' => ['class'=> 'form-check-input'],
					'required' => false
				]
			)
		;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Currency::class,
        ]);
    }
}
