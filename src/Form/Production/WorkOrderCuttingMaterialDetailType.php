<?php

namespace App\Form\Production;

use App\Entity\Production\WorkOrderCuttingMaterialDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkOrderCuttingMaterialDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shiftNumber')
            ->add('cuttingDate', null, ['widget' => 'single_text'])
            ->add('cuttingStartTime')
            ->add('cuttingEndTime')
            ->add('cuttingQuantityRim')
            ->add('cuttingQuantityDreek')
            ->add('memo')
            ->add('isCanceled')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkOrderCuttingMaterialDetail::class,
        ]);
    }
}
