<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressSelectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // set the select box display text and value
        $choices = [];
        foreach ($options['addresses'] as $address) {
            $text = sprintf(
                '%s %s %s',
                $address['address1'],
                $address['address2'],
                $address['postcode']
            );
            $choices[$text] = $address['id'];
        }

        $builder->add('address', ChoiceType::class, [
            'choices'  => $choices,
            'placeholder' => 'Choose Address',
            'label' => false
        ]);
        $builder->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => null]);
        $resolver->setRequired('addresses');
    }
}
