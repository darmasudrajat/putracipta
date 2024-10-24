<?php

namespace App\Form\Sale;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Sale\SaleInvoiceDetail;
use App\Entity\Sale\DeliveryDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleInvoiceDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isCanceled')
            ->add('deliveryDetail', EntityHiddenType::class, ['class' => DeliveryDetail::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SaleInvoiceDetail::class,
        ]);
    }
}
