<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Production\MasterOrderProductDetail;
use App\Entity\Sale\SaleOrderDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MasterOrderProductDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isCanceled')
            ->add('quantityStock', FormattedNumberType::class, ['decimals' => 0])
            ->add('quantityProduction', FormattedNumberType::class, ['decimals' => 0])
            ->add('saleOrderDetail', EntityHiddenType::class, ['class' => SaleOrderDetail::class])
            ->add('quantityPrinting')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MasterOrderProductDetail::class,
        ]);
    }
}
