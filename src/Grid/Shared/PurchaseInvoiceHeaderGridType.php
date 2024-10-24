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

class PurchaseInvoiceHeaderGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['supplierInvoiceCodeNumber', 'transactionDate', 'grandTotal', 'totalPayment', 'totalReturn', 'remainingPayment', 'transactionStatus'],
                'field_label_list' => [
                    'codeNumberOrdinal' => 'Code Number',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => '',
                    'transactionDate' => 'Tanggal',
                    'supplierInvoiceCodeNumber' => 'Supplier Invoice #',
                ],
                'field_operators_list' => [
                    'supplierInvoiceCodeNumber' => [FilterContain::class, FilterNotContain::class],
                    'transactionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'grandTotal' => [FilterEqual::class, FilterNotEqual::class],
                    'totalPayment' => [FilterEqual::class, FilterNotEqual::class],
                    'totalReturn' => [FilterEqual::class, FilterNotEqual::class],
                    'remainingPayment' => [FilterEqual::class, FilterNotEqual::class],
                    'transactionStatus' => [FilterContain::class, FilterNotContain::class],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['supplierInvoiceCodeNumber', 'transactionDate', 'grandTotal', 'totalPayment', 'totalReturn', 'remainingPayment', 'transactionStatus'],
                'field_label_list' => [
                    'codeNumberOrdinal' => 'Code Number',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => '',
                    'transactionDate' => 'Tanggal',
                    'supplierInvoiceCodeNumber' => 'Supplier Invoice #',
                ],
                'field_operators_list' => [
                    'supplierInvoiceCodeNumber' => [SortAscending::class, SortDescending::class],
                    'transactionDate' => [SortAscending::class, SortDescending::class],
                    'grandTotal' => [SortAscending::class, SortDescending::class],
                    'totalPayment' => [SortAscending::class, SortDescending::class],
                    'totalReturn' => [SortAscending::class, SortDescending::class],
                    'remainingPayment' => [SortAscending::class, SortDescending::class],
                    'transactionStatus' => [SortAscending::class, SortDescending::class],
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
