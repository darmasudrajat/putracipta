<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Production\MasterOrderHeader;
use App\Entity\Production\WorkOrderVarnishSpotHeader;
use App\Entity\Production\WorkOrderVarnishSpotProductionDetail;
use App\Entity\Production\WorkOrderVarnishSpotSettingDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkOrderVarnishSpotHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('varnishType')
            ->add('uvOilQuantity')
            ->add('alcoholQuantity')
            ->add('wbQuantity')
            ->add('workOrderReturnDate', null, ['widget' => 'single_text'])
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('masterOrderHeader', EntityHiddenType::class, ['class' => MasterOrderHeader::class])
            ->add('employeeIdWorkOrderReturn', null, ['choice_label' => 'name'])
            ->add('workOrderVarnishSpotSettingDetails', CollectionType::class, [
                'entry_type' => WorkOrderVarnishSpotSettingDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new WorkOrderVarnishSpotSettingDetail(),
                'label' => false,
            ])
            ->add('workOrderVarnishSpotProductionDetails', CollectionType::class, [
                'entry_type' => WorkOrderVarnishSpotProductionDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new WorkOrderVarnishSpotProductionDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkOrderVarnishSpotHeader::class,
        ]);
    }
}
