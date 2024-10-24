<?php

namespace App\Form\Purchase;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedDateType;
use App\Entity\Purchase\PurchaseOrderHeader;
use App\Entity\Purchase\PurchaseOrderPaperHeader;
use App\Entity\Purchase\ReceiveDetail;
use App\Entity\Purchase\ReceiveHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReceiveHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('supplierDeliveryCodeNumber')
            ->add('transactionDate', FormattedDateType::class)
            ->add('warehouse', null, [
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false")
                            ->addOrderBy('e.name', 'ASC');
                },
            ])
            ->add('note')
            ->add('receiveDetails', CollectionType::class, [
                'entry_type' => ReceiveDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new ReceiveDetail(),
                'label' => false,
            ])
            ->add('purchaseOrderHeader', EntityHiddenType::class, ['class' => PurchaseOrderHeader::class])
            ->add('purchaseOrderPaperHeader', EntityHiddenType::class, ['class' => PurchaseOrderPaperHeader::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReceiveHeader::class,
        ]);
    }
}
