<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Production\MasterOrderHeader;
use App\Entity\Production\WorkOrderOffsetPrintingHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// TEST
use App\Entity\Master\Supplier;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderHeader;


class WorkOrderOffsetPrintingHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
      
            ->add('masterOrderHeader', EntityHiddenType::class, ['class' => MasterOrderHeader::class])
            // ->add('master_order_header_id', EntityHiddenType::class, ['class' => master_order_header_id::class]) 
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
            
            ->add('col1name')
            ->add('col1PrintingQuantity')
            ->add('col1InkQuantity')
            ->add('col1PlateQuantity')

            ->add('col2name')
            ->add('col2PrintingQuantity')
            ->add('col2InkQuantity')
            ->add('col2PlateQuantity')

            ->add('col3name')
            ->add('col3PrintingQuantity')
            ->add('col3InkQuantity')
            ->add('col3PlateQuantity')

            ->add('col4name')
            ->add('col4PrintingQuantity')
            ->add('col4InkQuantity')
            ->add('col4PlateQuantity')            


            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('supplier', EntityHiddenType::class, ['class' => Supplier::class])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkOrderOffsetPrintingHeader::class,
        ]);
    }
}
