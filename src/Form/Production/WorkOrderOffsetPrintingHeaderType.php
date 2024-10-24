<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Production\MasterOrderHeader;
use App\Entity\Production\WorkOrderOffsetPrintingHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkOrderOffsetPrintingHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('printingQuality')
            ->add('speedPerHour')
            ->add('waterLevel')
            ->add('alcoholLevel')
            ->add('fountainLevel')
            ->add('wbLevel')
            ->add('cyanPrintingQuantity')
            ->add('cyanInkQuantity')
            ->add('cyanPlateQuantity')
            ->add('magentaPrintingQuantity')
            ->add('magentaInkQuantity')
            ->add('magentaPlateQuantity')
            ->add('yellowPrintingQuantity')
            ->add('yellowInkQuantity')
            ->add('yellowPlateQuantity')
            ->add('blackPrintingQuantity')
            ->add('blackInkQuantity')
            ->add('blackPlateQuantity')
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('masterOrderHeader', EntityHiddenType::class, ['class' => MasterOrderHeader::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkOrderOffsetPrintingHeader::class,
        ]);
    }
}
