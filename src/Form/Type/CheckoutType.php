<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class CheckoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $sandbox['card']   = '5424000000000015';
        $sandbox['expiry'] = '1220';
        $sandbox['cvs']    = '999';

        $builder->add('card', NumberType::class, [
            'label' => 'Card Number',
            'attr' => ['value' => $sandbox['card']]
        ]);
        $builder->add('expiry', NumberType::class, [
            'label' => 'Expiry Date',
            'attr' => ['value' => $sandbox['expiry'] , 'class' => 'small']
        ]);
        $builder->add('cvs', NumberType::class, [
            'label' => 'CVS',
            'attr' => ['value' => $sandbox['cvs'] , 'class' => 'small']
        ]);
        $builder->add('submit', SubmitType::class);
    }
}
