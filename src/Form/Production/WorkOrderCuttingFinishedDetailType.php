<?php

namespace App\Form\Production;

use App\Entity\Production\WorkOrderCuttingFinishedDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkOrderCuttingFinishedDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shiftNumber')
            ->add('cuttingDate', null, ['widget' => 'single_text'])
            ->add('cuttingStartTime')
            ->add('cuttingEndTime')
            ->add('cuttingQuantityDreek')
            ->add('cuttingQuantityPiece')
            ->add('memo')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkOrderCuttingFinishedDetail::class,
        ]);
    }
}
