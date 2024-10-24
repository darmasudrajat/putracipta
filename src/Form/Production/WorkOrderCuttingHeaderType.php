<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
//use App\Entity\Production\WorkOrderCuttingFinishedDetail;
use App\Entity\Production\WorkOrderCuttingHeader;
use App\Entity\Production\WorkOrderCuttingMaterialDetail;
use App\Entity\Production\MasterOrderHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkOrderCuttingHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isSizeFit')
            ->add('workOrderReturnDate', null, ['widget' => 'single_text'])
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('masterOrderHeader', EntityHiddenType::class, ['class' => MasterOrderHeader::class])
            ->add('employeeIdWorkOrderReturn', null, ['choice_label' => 'name'])
            ->add('workOrderCuttingMaterialDetails', CollectionType::class, [
                'entry_type' => WorkOrderCuttingMaterialDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new WorkOrderCuttingMaterialDetail(),
                'label' => false,
            ])
//            ->add('workOrderCuttingFinishedDetails', CollectionType::class, [
//                'entry_type' => WorkOrderCuttingFinishedDetailType::class,
//                'allow_add' => true,
//                'allow_delete' => true,
//                'by_reference' => false,
//                'prototype_data' => new WorkOrderCuttingFinishedDetail(),
//                'label' => false,
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkOrderCuttingHeader::class,
        ]);
    }
}
