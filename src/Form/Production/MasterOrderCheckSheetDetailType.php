<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\DesignCodeCheckSheetDetail;
use App\Entity\Master\WorkOrderCheckSheet;
use App\Entity\Production\MasterOrderCheckSheetDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MasterOrderCheckSheetDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isSubcon')
//            ->add('isCanceled')
            ->add('workOrderCheckSheet', EntityHiddenType::class, ['class' => WorkOrderCheckSheet::class])
            ->add('designCodeCheckSheetDetail', EntityHiddenType::class, ['class' => DesignCodeCheckSheetDetail::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MasterOrderCheckSheetDetail::class,
        ]);
    }
}
