<?php

namespace App\Form\Purchase;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedDateType;
use App\Entity\Purchase\PurchaseInvoiceDetail;
use App\Entity\Purchase\PurchaseInvoiceHeader;
use App\Entity\Purchase\ReceiveHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseInvoiceHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('invoiceTaxCodeNumber')
            ->add('supplierInvoiceCodeNumber')
            ->add('transactionDate', FormattedDateType::class)
            ->add('invoiceReceivedDate', FormattedDateType::class)
            ->add('invoiceTaxDate', FormattedDateType::class)
            ->add('note')
            ->add('receiveHeader', EntityHiddenType::class, ['class' => ReceiveHeader::class])
            ->add('purchaseInvoiceDetails', CollectionType::class, [
                'entry_type' => PurchaseInvoiceDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchaseInvoiceDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseInvoiceHeader::class,
        ]);
    }
}
