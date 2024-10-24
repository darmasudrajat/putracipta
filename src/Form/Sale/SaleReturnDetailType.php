<?php

namespace App\Form\Sale;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Sale\DeliveryDetail;
use App\Entity\Sale\SaleReturnDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleReturnDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', FormattedNumberType::class, ['decimals' => 0])
            ->add('deliveryDetail', EntityHiddenType::class, ['class' => DeliveryDetail::class])
            ->add('isCanceled')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SaleReturnDetail::class,
        ]);
    }
}
