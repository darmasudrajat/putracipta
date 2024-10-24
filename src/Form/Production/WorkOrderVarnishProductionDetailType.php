<?php

namespace App\Form\Production;

use App\Entity\Production\WorkOrderVarnishProductionDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkOrderVarnishProductionDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shiftNumber')
            ->add('productionDate', null, ['widget' => 'single_text'])
            ->add('productionStartTime')
            ->add('productionEndTime')
            ->add('productionOutputQuantity')
            ->add('productionRejectQuantity')
            ->add('memo')
            ->add('opvUsageQuantity')
            ->add('uvUsageQuantity')
            ->add('alcoholUsageQuantity')
            ->add('wbUsageQuantity')
            ->add('isCanceled')
            ->add('employeeIdOperator', null, ['choice_label' => 'name'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkOrderVarnishProductionDetail::class,
        ]);
    }
}
