<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Production\MasterOrderHeader;
use App\Entity\Production\WorkOrderColorMixing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkOrderColorMixingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('paperInseetUsedUsage')
            ->add('paperInseetNewUsage')
            ->add('specialColorMixFirstOneName')
            ->add('specialColorMixFirstOneWeight')
            ->add('specialColorMixFirstTwoName')
            ->add('specialColorMixFirstTwoWeight')
            ->add('specialColorMixFirstThreeName')
            ->add('specialColorMixFirstThreeWeight')
            ->add('specialColorMixFirstFourName')
            ->add('specialColorMixFirstFourWeight')
            ->add('specialColorMixSecondOneName')
            ->add('specialColorMixSecondOneWeight')
            ->add('specialColorMixSecondTwoName')
            ->add('specialColorMixSecondTwoWeight')
            ->add('specialColorMixSecondThreeName')
            ->add('specialColorMixSecondThreeWeight')
            ->add('specialColorMixSecondFourName')
            ->add('specialColorMixSecondFourWeight')
            ->add('specialColorMixThirdOneName')
            ->add('specialColorMixThirdOneWeight')
            ->add('specialColorMixThirdTwoName')
            ->add('specialColorMixThirdTwoWeight')
            ->add('specialColorMixThirdThreeName')
            ->add('specialColorMixThirdThreeWeight')
            ->add('specialColorMixThirdFourName')
            ->add('specialColorMixThirdFourWeight')
            ->add('specialColorMixFourthOneName')
            ->add('specialColorMixFourthOneWeight')
            ->add('specialColorMixFourthTwoName')
            ->add('specialColorMixFourthTwoWeight')
            ->add('specialColorMixFourthThreeName')
            ->add('specialColorMixFourthThreeWeight')
            ->add('specialColorMixFourthFourName')
            ->add('specialColorMixFourthFourWeight')
            ->add('note')
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('masterOrderHeader', EntityHiddenType::class, ['class' => MasterOrderHeader::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkOrderColorMixing::class,
        ]);
    }
}
