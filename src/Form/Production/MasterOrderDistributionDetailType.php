<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\DesignCodeDistributionDetail;
use App\Entity\Master\WorkOrderDistribution;
use App\Entity\Production\MasterOrderDistributionDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MasterOrderDistributionDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isSubcon')
//            ->add('isCanceled')
            ->add('workOrderDistribution', EntityHiddenType::class, ['class' => WorkOrderDistribution::class])
            ->add('designCodeDistributionDetail', EntityHiddenType::class, ['class' => DesignCodeDistributionDetail::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MasterOrderDistributionDetail::class,
        ]);
    }
}
