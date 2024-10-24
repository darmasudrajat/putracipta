<?php

namespace App\Form\Production;

use App\Entity\Production\WorkOrderVarnishSpotSettingDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkOrderVarnishSpotSettingDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shiftNumber')
            ->add('settingDate', null, ['widget' => 'single_text'])
            ->add('settingStartTime')
            ->add('settingEndTime')
            ->add('memo')
            ->add('isCanceled')
            ->add('employeeIdOperator', null, ['choice_label' => 'name'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkOrderVarnishSpotSettingDetail::class,
        ]);
    }
}
