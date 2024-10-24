<?php

namespace App\Form\Production;

use App\Entity\Production\WorkOrderOffsetPrintingDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkOrderOffsetPrintingDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shiftNumber')
            ->add('transactionDate')
            ->add('productionColor')
            ->add('productionStartTime')
            ->add('productionEndTime')
            ->add('productionOutputStartTime')
            ->add('productionOutputEndTime')
            ->add('memo')
            ->add('isCanceled')
            ->add('workOrderOffsetPrintingHeader')
            ->add('employeeIdOperator')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkOrderOffsetPrintingDetail::class,
        ]);
    }
}
