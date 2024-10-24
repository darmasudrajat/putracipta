<?php

namespace App\Form\Master;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\DesignCodeDistributionDetail;
use App\Entity\Master\WorkOrderDistribution;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DesignCodeDistributionDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isInactive')
            ->add('workOrderDistribution', EntityHiddenType::class, ['class' => WorkOrderDistribution::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DesignCodeDistributionDetail::class,
        ]);
    }
}
