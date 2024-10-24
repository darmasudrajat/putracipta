<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Production\MasterOrderHeader;
use App\Entity\Production\WorkOrderPrepress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkOrderPrepressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantityPlateNew')
            ->add('quantityPlateOld')
            ->add('quantityPlateUsed')
            ->add('note')
            ->add('plateBrand')
            ->add('internalCyanBeginningOutputStartDate', null, ['widget' => 'single_text'])
            ->add('internalCyanBeginningOutputEndDate', null, ['widget' => 'single_text'])
            ->add('internalMagentaBeginningOutputStartDate', null, ['widget' => 'single_text'])
            ->add('internalMagentaBeginningOutputEndDate', null, ['widget' => 'single_text'])
            ->add('internalYellowBeginningOutputStartDate', null, ['widget' => 'single_text'])
            ->add('internalYellowBeginningOutputEndDate', null, ['widget' => 'single_text'])
            ->add('internalBlackBeginningOutputStartDate', null, ['widget' => 'single_text'])
            ->add('internalBlackBeginningOutputEndDate', null, ['widget' => 'single_text'])
            ->add('internalCyanRevisionStartDate', null, ['widget' => 'single_text'])
            ->add('internalCyanRevisionEndDate', null, ['widget' => 'single_text'])
            ->add('internalMagentaRevisionStartDate', null, ['widget' => 'single_text'])
            ->add('internalMagentaRevisionEndDate', null, ['widget' => 'single_text'])
            ->add('internalYellowRevisionStartDate', null, ['widget' => 'single_text'])
            ->add('internalYellowRevisionEndDate', null, ['widget' => 'single_text'])
            ->add('internalBlackRevisionStartDate', null, ['widget' => 'single_text'])
            ->add('internalBlackRevisionEndDate', null, ['widget' => 'single_text'])
            ->add('internalCyanDowntimeStartDate', null, ['widget' => 'single_text'])
            ->add('internalCyanDowntimeEndDate', null, ['widget' => 'single_text'])
            ->add('internalCyanDowntimeMemo')
            ->add('internalMagentaDowntimeStartDate', null, ['widget' => 'single_text'])
            ->add('internalMagentaDowntimeEndDate', null, ['widget' => 'single_text'])
            ->add('internalMagentaDowntimeMemo')
            ->add('internalYellowDowntimeStartDate', null, ['widget' => 'single_text'])
            ->add('internalYellowDowntimeEndDate', null, ['widget' => 'single_text'])
            ->add('internalYellowDowntimeMemo')
            ->add('internalBlackDowntimeStartDate', null, ['widget' => 'single_text'])
            ->add('internalBlackDowntimeEndDate', null, ['widget' => 'single_text'])
            ->add('internalBlackDowntimeMemo')
            ->add('subconCyanBeginningOutputStartDate', null, ['widget' => 'single_text'])
            ->add('subconCyanBeginningOutputEndDate', null, ['widget' => 'single_text'])
            ->add('subconMagentaBeginningOutputStartDate', null, ['widget' => 'single_text'])
            ->add('subconMagentaBeginningOutputEndDate', null, ['widget' => 'single_text'])
            ->add('subconYellowBeginningOutputStartDate', null, ['widget' => 'single_text'])
            ->add('subconYellowBeginningOutputEndDate', null, ['widget' => 'single_text'])
            ->add('subconBlackBeginningOutputStartDate', null, ['widget' => 'single_text'])
            ->add('subconBlackBeginningOutputEndDate', null, ['widget' => 'single_text'])
            ->add('subconCyanRevisionStartDate', null, ['widget' => 'single_text'])
            ->add('subconCyanRevisionEndDate', null, ['widget' => 'single_text'])
            ->add('subconMagentaRevisionStartDate', null, ['widget' => 'single_text'])
            ->add('subconMagentaRevisionEndDate', null, ['widget' => 'single_text'])
            ->add('subconYellowRevisionStartDate', null, ['widget' => 'single_text'])
            ->add('subconYellowRevisionEndDate', null, ['widget' => 'single_text'])
            ->add('subconBlackRevisionStartDate', null, ['widget' => 'single_text'])
            ->add('subconBlackRevisionEndDate', null, ['widget' => 'single_text'])
            ->add('workOrderReturnDate', null, ['widget' => 'single_text'])
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('employeeIdPlateRelease', null, ['choice_label' => 'name'])
            ->add('employeeIdWorkOrderReturn', null, ['choice_label' => 'name'])
            ->add('masterOrderHeader', EntityHiddenType::class, ['class' => MasterOrderHeader::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkOrderPrepress::class,
        ]);
    }
}
