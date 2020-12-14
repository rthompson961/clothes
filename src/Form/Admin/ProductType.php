<?php

namespace App\Form\Admin;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Colour;
use App\Entity\Product;
use App\Entity\ProductGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('image', FileType::class, [
            'label' => 'Image (315x472)',
            'mapped' => false,
            'constraints' => [
                new File([
                    'maxSize' => '50k',
                    'maxSizeMessage' => 'The file is too large, maximum size is 50 kB.',
                    'mimeTypes' => [
                        'image/jpeg'
                    ],
                    'mimeTypesMessage' => 'Please upload a valid JPG image.'
                ])
            ]
        ]);
        $builder->add('name', TextType::class);
        $builder->add('price', TextType::class, ['attr' => ['class' => 'small']]);
        $builder->add('category', EntityType::class, [
            'class' => Category::class,
            'choice_label' => 'name',
            'placeholder' => 'Choose Category'
        ]);
        $builder->add('brand', EntityType::class, [
            'class' => Brand::class,
            'choice_label' => 'name',
            'placeholder' => 'Choose Brand'
        ]);
        $builder->add('colour', EntityType::class, [
            'class' => Colour::class,
            'choice_label' => 'name',
            'placeholder' => 'Choose Colour'
        ]);
        $builder->add('product_group', EntityType::class, [
            'class' => ProductGroup::class,
            'choice_label' => 'name',
            'placeholder' => 'Choose Product Group',
            'required' => false
        ]);
        $builder->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
