<?php

namespace App\Form\Master;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\DesignCodeCheckSheetDetail;
use App\Entity\Master\WorkOrderCheckSheet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DesignCodeCheckSheetDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isInactive')
            ->add('workOrderCheckSheet', EntityHiddenType::class, ['class' => WorkOrderCheckSheet::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DesignCodeCheckSheetDetail::class,
        ]);
    }
}
