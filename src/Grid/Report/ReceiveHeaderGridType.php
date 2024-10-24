<?php

namespace App\Grid\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Common\Data\Operator\FilterContain;
use App\Common\Data\Operator\FilterEqual;
use App\Common\Data\Operator\FilterNotBetween;
use App\Common\Data\Operator\FilterNotContain;
use App\Common\Data\Operator\FilterNotEqual;
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\FilterType;
use App\Common\Form\Type\PaginationType;
use App\Common\Form\Type\SortType;
use App\Entity\Master\Warehouse;
use App\Entity\PurchaseHeader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReceiveHeaderGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'purchaseOrderCodeNumberOrdinal', 'purchaseOrderCodeNumberMonth', 'purchaseOrderCodeNumberYear', 'transactionDate', 'warehouse', 'supplier:company', 'supplierDeliveryCodeNumber', 'note'],
                'field_label_list' => [
                    'codeNumberOrdinal' => 'Code Number',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => '',
                    'purchaseOrderCodeNumberOrdinal' => 'PO Number',
                    'purchaseOrderCodeNumberMonth' => '',
                    'purchaseOrderCodeNumberYear' => '',
                    'transactionDate' => 'Tanggal',
                    'supplierDeliveryCodeNumber' => 'SJ Supplier #',
                    'supplier:company' => 'Supplier',
                    'warehouse' => 'Gudang',
                ],
                'field_operators_list' => [
                    'supplierDeliveryCodeNumber' => [FilterContain::class, FilterNotContain::class],
                    'codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderCodeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderCodeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderCodeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'transactionDate' => [FilterBetween::class, FilterNotBetween::class],
                    'supplier:company' => [FilterContain::class, FilterNotContain::class],
                    'warehouse' => [FilterEqual::class, FilterNotEqual::class],
                    'note' => [FilterContain::class, FilterNotContain::class],
                ],
                'field_value_type_list' => [
                    'codeNumberOrdinal' => IntegerType::class,
                    'codeNumberMonth' => ChoiceType::class,
                    'codeNumberYear' => IntegerType::class,
                    'purchaseOrderCodeNumberOrdinal' => IntegerType::class,
                    'purchaseOrderCodeNumberMonth' => ChoiceType::class,
                    'purchaseOrderCodeNumberYear' => IntegerType::class,
                    'warehouse' => EntityType::class,
                ],
                'field_value_options_list' => [
                    'codeNumberMonth' => ['choices' => array_flip(PurchaseHeader::MONTH_ROMAN_NUMERALS)],
                    'purchaseOrderCodeNumberMonth' => ['choices' => array_flip(PurchaseHeader::MONTH_ROMAN_NUMERALS)],
                    'transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'warehouse' => ['class' => Warehouse::class, 'choice_label' => 'name'],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['transactionDate', 'warehouse', 'supplier:company', 'supplierDeliveryCodeNumber',  'note', 'purchaseOrderCodeNumberYear', 'purchaseOrderCodeNumberMonth', 'purchaseOrderCodeNumberOrdinal', 'codeNumberYear', 'codeNumberMonth', 'codeNumberOrdinal'],
                'field_label_list' => [
                    'codeNumberOrdinal' => '',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => 'Code Number',
                    'purchaseOrderCodeNumberOrdinal' => '',
                    'purchaseOrderCodeNumberMonth' => '',
                    'purchaseOrderCodeNumberYear' => 'PO #',
                    'transactionDate' => 'Tanggal',
                    'supplierDeliveryCodeNumber' => 'SJ Supplier #',
                    'supplier:company' => 'Supplier',
                    'warehouse' => 'Gudang',
                ],
                'field_operators_list' => [
                    'supplierDeliveryCodeNumber' => [SortAscending::class, SortDescending::class],
                    'codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderCodeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderCodeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderCodeNumberYear' => [SortAscending::class, SortDescending::class],
                    'transactionDate' => [SortAscending::class, SortDescending::class],
                    'supplier:company' => [SortAscending::class, SortDescending::class],
                    'warehouse' => [SortAscending::class, SortDescending::class],
                    'note' => [SortAscending::class, SortDescending::class],
                ],
            ])
            ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DataCriteria::class,
            'csrf_protection' => false,
        ]);
    }
}
