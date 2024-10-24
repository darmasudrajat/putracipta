<?php

namespace App\Grid\Sale;

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
use App\Entity\Sale\SaleOrderHeader;
use App\Entity\SaleHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleOrderHeaderGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'transactionDate', 'referenceNumber', 'customer:company', 'note', 'transactionStatus'],
                'field_label_list' => [
                    'codeNumberOrdinal' => 'Code Number',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => '',
                    'transactionDate' => 'Tanggal',
                    'customer:company' => 'Customer',
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'transactionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'customer:company' => [FilterContain::class, FilterNotContain::class],
                    'referenceNumber' => [FilterContain::class, FilterNotContain::class],
                    'note' => [FilterContain::class, FilterNotContain::class],
                    'transactionStatus' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    'codeNumberOrdinal' => IntegerType::class,
                    'codeNumberMonth' => ChoiceType::class,
                    'codeNumberYear' => IntegerType::class,
                    'transactionStatus' => ChoiceType::class,
                ],
                'field_value_options_list' => [
                    'codeNumberMonth' => ['choices' => array_flip(SaleHeader::MONTH_ROMAN_NUMERALS)],
                    'transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'transactionStatus' => ['choices' => [
                        'Approved' => SaleOrderHeader::TRANSACTION_STATUS_APPROVE, 
                        'Completed' => SaleOrderHeader::TRANSACTION_STATUS_DONE,
                        'Draft' => SaleOrderHeader::TRANSACTION_STATUS_DRAFT,
                        'Delivered' => SaleOrderHeader::TRANSACTION_STATUS_FULL_DELIVERY,
                        'On hold' => SaleOrderHeader::TRANSACTION_STATUS_HOLD,
                        'Partial delivery' => SaleOrderHeader::TRANSACTION_STATUS_PARTIAL_DELIVERY,
                        'Rejected' => SaleOrderHeader::TRANSACTION_STATUS_REJECT,
                        'Released' => SaleOrderHeader::TRANSACTION_STATUS_RELEASE,
                        'Internal' => SaleOrderHeader::TRANSACTION_TYPE_INTERNAL,
                        'On production' => SaleOrderHeader::TRANSACTION_TYPE_PRODUCTION
                    ]],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['transactionDate', 'customer:company', 'referenceNumber', 'note', 'transactionStatus', 'codeNumberYear', 'codeNumberMonth', 'codeNumberOrdinal'],
                'field_label_list' => [
                    'codeNumberOrdinal' => '',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => 'Code Number',
                    'transactionDate' => 'Tanggal',
                    'customer:company' => 'Customer',
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'transactionDate' => [SortAscending::class, SortDescending::class],
                    'customer:company' => [SortAscending::class, SortDescending::class],
                    'referenceNumber' => [SortAscending::class, SortDescending::class],
                    'note' => [SortAscending::class, SortDescending::class],
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
