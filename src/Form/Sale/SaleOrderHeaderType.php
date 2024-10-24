<?php

namespace App\Form\Sale;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedDateType;
use App\Entity\Master\Customer;
use App\Entity\Sale\SaleOrderDetail;
use App\Entity\Sale\SaleOrderHeader;
use App\Repository\Master\DivisionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SaleOrderHeaderType extends AbstractType
{
    private DivisionRepository $divisionRepository;

    public function __construct(DivisionRepository $divisionRepository)
    {
        $this->divisionRepository = $divisionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', FormattedDateType::class)
            ->add('orderReceiveDate', FormattedDateType::class)
            ->add('note')
            ->add('referenceNumber')
            ->add('discountValueType', ChoiceType::class, ['choices' => [
                'Percentage' => SaleOrderHeader::DISCOUNT_VALUE_TYPE_PERCENTAGE,
                'Nominal' => SaleOrderHeader::DISCOUNT_VALUE_TYPE_NOMINAL,
            ]])
            ->add('discountValue')
            ->add('isUsingFscPaper')
            ->add('employee', null, [
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                        ->andWhere("e.division = :division")->setParameter('division', $this->divisionRepository->findMarketingRecord())
                        ->andWhere("e.isInactive = false");
                },
            ])
            ->add('taxMode', ChoiceType::class, ['choices' => [
                'Non PPn' => SaleOrderHeader::TAX_MODE_NON_TAX,
                'Exclude PPn' => SaleOrderHeader::TAX_MODE_TAX_EXCLUSION,
                'Include PPn' => SaleOrderHeader::TAX_MODE_TAX_INCLUSION,
            ]])
            ->add('saleOrderDetails', CollectionType::class, [
                'entry_type' => SaleOrderDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new SaleOrderDetail(),
                'label' => false,
            ])
            ->add('deliveryAddressOrdinal', TextType::class)
            ->add('customerName', TextType::class)
            ->add('customer', EntityHiddenType::class, ['class' => Customer::class])
            ->add('transactionType', ChoiceType::class, ['choices' => [
                'Produksi' => SaleOrderHeader::TRANSACTION_TYPE_PRODUCTION,
                'Internal' => SaleOrderHeader::TRANSACTION_TYPE_INTERNAL,
            ]])
            ->add('transactionFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '12000k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'application/pdf',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPEG, PNG, PDF, or Excel',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SaleOrderHeader::class,
        ]);
    }
}
