<?php

namespace App\Grid\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterContain;
use App\Common\Data\Operator\FilterEqual;
use App\Common\Data\Operator\FilterNotContain;
use App\Common\Data\Operator\FilterNotEqual;
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\FilterType;
use App\Common\Form\Type\PaginationType;
use App\Common\Form\Type\SortType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MasterOrderHeaderGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'transactionDate', 'masterOrderProductNameList', 'masterOrderProductList', 'saleOrderReferenceNumberList', 'designCode:code', 'designCode:variant', 'designCode:version', 'customer:company'],
                'field_label_list' => [
                    'codeNumberOrdinal' => 'Code Number',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => '',
                    'transactionDate' => 'Tanggal',
                    'designCode:code' => 'Design Code',
                    'customer:company' => 'Customer',
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'transactionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'customer:company' => [FilterContain::class, FilterNotContain::class],
                    'designCode:code' => [FilterContain::class, FilterNotContain::class],
                    'designCode:variant' => [FilterContain::class, FilterNotContain::class],
                    'designCode:version' => [FilterContain::class, FilterNotContain::class],
                    'saleOrderReferenceNumberList' => [FilterContain::class, FilterNotContain::class],
                    'masterOrderProductList' => [FilterContain::class, FilterNotContain::class],
                    'masterOrderProductNameList' => [FilterContain::class, FilterNotContain::class],
                ],
                'field_value_type_list' => [
                    'codeNumberOrdinal' => IntegerType::class,
                    'codeNumberMonth' => ChoiceType::class,
                    'codeNumberYear' => IntegerType::class,
                ],
                'field_value_options_list' => [
                    'codeNumberMonth' => ['choices' => array_flip(ProductionHeader::MONTH_ROMAN_NUMERALS)],
                    'transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'transactionDate', 'masterOrderProductNameList', 'masterOrderProductList', 'saleOrderReferenceNumberList', 'customer:company', 'designCode:code', 'designCode:variant', 'designCode:version'],
                'field_label_list' => [
                    'codeNumberOrdinal' => '',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => 'Code Number',
                    'transactionDate' => 'Tanggal',
                    'customer:company' => 'Customer',
                    'designCode:code' => 'Design Code',
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'transactionDate' => [SortAscending::class, SortDescending::class],
                    'customer:company' => [SortAscending::class, SortDescending::class],
                    'saleOrderReferenceNumberList' => [SortAscending::class, SortDescending::class],
                    'masterOrderProductNameList' => [SortAscending::class, SortDescending::class],
                    'masterOrderProductList' => [SortAscending::class, SortDescending::class],
                    'designCode:code' => [SortAscending::class, SortDescending::class],
                    'designCode:variant' => [SortAscending::class, SortDescending::class],
                    'designCode:version' => [SortAscending::class, SortDescending::class],
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
