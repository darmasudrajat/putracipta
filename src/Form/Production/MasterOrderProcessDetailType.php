<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\DesignCodeProcessDetail;
use App\Entity\Master\WorkOrderProcess;
use App\Entity\Production\MasterOrderProcessDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MasterOrderProcessDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isSubcon')
//            ->add('isCanceled')
            ->add('workOrderProcess', EntityHiddenType::class, ['class' => WorkOrderProcess::class])
            ->add('designCodeProcessDetail', EntityHiddenType::class, ['class' => DesignCodeProcessDetail::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MasterOrderProcessDetail::class,
        ]);
    }
}
