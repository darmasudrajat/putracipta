<?php

namespace App\Form\Master;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\DesignCodeProcessDetail;
use App\Entity\Master\WorkOrderProcess;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DesignCodeProcessDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isInactive')
            ->add('workOrderProcess', EntityHiddenType::class, ['class' => WorkOrderProcess::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DesignCodeProcessDetail::class,
        ]);
    }
}
