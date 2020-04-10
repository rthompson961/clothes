<?php

namespace App\Form\Type;

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

        // list of 1 to 10 for quantity selection
        $quantities = [];
        for ($i = 1; $i <= 10; $i++) {
            $quantities[$i] = $i;
        }

        $builder->add('product', ChoiceType::class, [
            'choices'  => $sizes,
            'choice_attr' => $attr,
            'placeholder' => 'Choose Size',
            'label' => false
        ]);
        $builder->add('quantity', ChoiceType::class, [
            'choices'  => $quantities,
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
