<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Product;
use App\Entity\Production\ProductDevelopmentDetail;
use App\Entity\Production\ProductPrototypeDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDevelopmentDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productPrototypeDetail', EntityHiddenType::class, ['class' => ProductPrototypeDetail::class])
            ->add('product', EntityHiddenType::class, ['class' => Product::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductDevelopmentDetail::class,
        ]);
    }
}
