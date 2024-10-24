<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedDateType;
use App\Entity\Master\Customer;
use App\Entity\Production\MasterOrderHeader;
use App\Entity\Production\QualityControlSortingDetail;
use App\Entity\Production\QualityControlSortingHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QualityControlSortingHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('employeeInCharge')
            ->add('transactionDate', FormattedDateType::class)
            ->add('note')
            ->add('masterOrderHeader', EntityHiddenType::class, ['class' => MasterOrderHeader::class])
            ->add('customer', EntityHiddenType::class, ['class' => Customer::class])
            ->add('qualityControlSortingDetails', CollectionType::class, [
                'entry_type' => QualityControlSortingDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new QualityControlSortingDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QualityControlSortingHeader::class,
        ]);
    }
}
