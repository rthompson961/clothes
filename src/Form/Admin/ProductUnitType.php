<?php

namespace App\Form\Admin;

use App\Entity\Product;
use App\Entity\ProductUnit;
use App\Entity\Size;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductUnitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('stock', TextType::class, ['attr' => ['class' => 'small']]);
        $builder->add('product', EntityType::class, [
            'class' => Product::class,
            'choice_label' => 'name',
            'placeholder' => 'Choose Product'
        ]);
        $builder->add('size', EntityType::class, [
            'class' => Size::class,
            'choice_label' => 'name',
            'placeholder' => 'Choose Size'
        ]);
        $builder->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductUnit::class,
        ]);
    }
}
