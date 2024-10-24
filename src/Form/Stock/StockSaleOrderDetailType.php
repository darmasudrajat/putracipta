<?php

namespace App\Form\Stock;

use App\Common\Form\Type\FormattedDateType;
use App\Entity\Sale\SaleOrderDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockSaleOrderDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('deliveryDate', FormattedDateType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SaleOrderDetail::class,
        ]);
    }
}
