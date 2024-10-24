<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Production\MasterOrderHeader;
use App\Entity\Production\WorkOrderVarnishHeader;
use App\Entity\Production\WorkOrderVarnishProductionDetail;
use App\Entity\Production\WorkOrderVarnishSettingDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkOrderVarnishHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('uvOilQuantity')
            ->add('alcoholQuantity')
            ->add('wbQuantity')
            ->add('workOrderReturnDate', null, ['widget' => 'single_text'])
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('masterOrderHeader', EntityHiddenType::class, ['class' => MasterOrderHeader::class])
            ->add('employeeIdWorkOrderReturn', null, ['choice_label' => 'name'])
            ->add('workOrderVarnishSettingDetails', CollectionType::class, [
                'entry_type' => WorkOrderVarnishSettingDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new WorkOrderVarnishSettingDetail(),
                'label' => false,
            ])
            ->add('workOrderVarnishProductionDetails', CollectionType::class, [
                'entry_type' => WorkOrderVarnishProductionDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new WorkOrderVarnishProductionDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkOrderVarnishHeader::class,
        ]);
    }
}
