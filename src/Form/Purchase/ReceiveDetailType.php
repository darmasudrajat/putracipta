<?php

namespace App\Form\Purchase;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use App\Entity\Purchase\ReceiveDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReceiveDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('receivedQuantity', FormattedNumberType::class, ['decimals' => 2])
            ->add('isCanceled')
            ->add('memo')
            ->add('purchaseOrderDetail', EntityHiddenType::class, ['class' => PurchaseOrderDetail::class])
            ->add('purchaseOrderPaperDetail', EntityHiddenType::class, ['class' => PurchaseOrderPaperDetail::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReceiveDetail::class,
        ]);
    }
}
