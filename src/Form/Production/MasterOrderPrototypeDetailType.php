<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Master\Product;
use App\Entity\Production\MasterOrderPrototypeDetail;
use App\Entity\Production\ProductPrototypeDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MasterOrderPrototypeDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isCanceled')
            ->add('quantityStock', FormattedNumberType::class, ['decimals' => 0])
            ->add('productPrototypeDetail', EntityHiddenType::class, ['class' => ProductPrototypeDetail::class])
            ->add('product', EntityHiddenType::class, ['class' => Product::class])
            ->add('quantityOrder')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MasterOrderPrototypeDetail::class,
        ]);
    }
}
