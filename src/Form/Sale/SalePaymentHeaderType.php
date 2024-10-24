<?php

namespace App\Form\Sale;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedDateType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Master\Customer;
use App\Entity\Sale\SalePaymentDetail;
use App\Entity\Sale\SalePaymentHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalePaymentHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', FormattedDateType::class)
            ->add('note')
            ->add('returnTransactionNumber')
            ->add('returnTaxNumber')
            ->add('administrationFee', FormattedNumberType::class, ['decimals' => 2])
            ->add('customer', EntityHiddenType::class, ['class' => Customer::class])
            ->add('paymentType', null, [
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                    ->andWhere("e.isInactive = false");
                },
            ])
            ->add('salePaymentDetails', CollectionType::class, [
                'entry_type' => SalePaymentDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new SalePaymentDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SalePaymentHeader::class,
        ]);
    }
}
