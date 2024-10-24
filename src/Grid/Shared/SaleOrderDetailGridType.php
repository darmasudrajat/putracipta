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

class SaleOrderDetailGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'transactionDate', 'saleOrderHeader:referenceNumber', 'product:name', 'product:code', 'unit:name'],
                'field_label_list' => [
                    'unit:name' => 'Satuan',
                    'product:name' => 'Material',
                    'product:code' => 'Code',
                    'saleOrderHeader:referenceNumber' => 'Customer PO #',
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'transactionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'unit:name' => [FilterContain::class, FilterNotContain::class],
                    'product:name' => [FilterContain::class, FilterNotContain::class],
                    'product:code' => [FilterContain::class, FilterNotContain::class],
                    'saleOrderHeader:referenceNumber' => [FilterContain::class, FilterNotContain::class],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'transactionDate', 'saleOrderHeader:referenceNumber', 'product:name', 'product:code', 'unit:name'],
                'field_label_list' => [
                    'unit:name' => 'Satuan',
                    'product:name' => 'Material',
                    'product:code' => 'Code',
                    'saleOrderHeader:referenceNumber' => 'Customer PO #',
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'transactionDate' => [SortAscending::class, SortDescending::class],
                    'unit:name' => [SortAscending::class, SortDescending::class],
                    'product:name' => [SortAscending::class, SortDescending::class],
                    'product:code' => [SortAscending::class, SortDescending::class],
                    'saleOrderHeader:referenceNumber' => [SortAscending::class, SortDescending::class],
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
