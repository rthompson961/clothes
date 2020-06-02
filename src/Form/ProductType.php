<?php

namespace App\Form;

use App\Repository\ProductUnitRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    private ProductUnitRepository $unitRepository;

    public function __construct(ProductUnitRepository $unitRepository)
    {
        $this->unitRepository = $unitRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Get each size for the current product
        $units = $this->unitRepository->findProductUnits($options['product']);
        $sizes = [];
        $attr  = [];
        foreach ($units as $unit) {
            $sizes[$unit['size']] = $unit['id'];

            if (!$unit['stock']) {
                $attr[$unit['size']] = ['disabled' => true];
            }
        }

        $builder->add('product', ChoiceType::class, [
            'choices'  => $sizes,
            'choice_attr' => $attr,
            'placeholder' => 'Choose Size',
            'label' => false
        ]);
        $builder->add('quantity', ChoiceType::class, [
            'choices'  => [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5],
            'label' => false
        ]);
        $builder->add('submit', SubmitType::class, ['label' => 'Add to Basket']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => null]);
        $resolver->setRequired('product');
    }
}
