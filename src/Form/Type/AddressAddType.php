<?php

namespace App\Form\Type;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('address1', TextType::class, ['label' => 'Address Line 1']);
        $builder->add('address2', TextType::class, ['label' => 'Address Line 2']);
        $builder->add('address3', TextType::class, [
            'label' => 'Address Line 3 (Optional)',
            'required' => false
        ]);
        $builder->add('county', TextType::class);
        $builder->add('postcode', TextType::class);
        $builder->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
