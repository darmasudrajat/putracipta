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
use App\Entity\Master\Supplier;
use App\Entity\PurchaseHeader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseInvoiceHeaderGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [
                    'codeNumberOrdinal', 
                    'codeNumberMonth', 
                    'codeNumberYear', 
                    'transactionDate', 
                    'supplierInvoiceCodeNumber', 
                    'supplier', 
                    'invoiceTaxCodeNumber',
                    'transactionStatus'
                ],
                'field_label_list' => [
                    'codeNumberOrdinal' => 'Code Number',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => '',
                    'transactionDate' => 'Tanggal',
                    'supplierInvoiceCodeNumber' => 'Supplier Invoice #',
                    'supplier' => 'Supplier',
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'supplierInvoiceCodeNumber' => [FilterContain::class, FilterNotContain::class],
                    'transactionDate' => [FilterBetween::class, FilterNotBetween::class],
                    'invoiceTaxCodeNumber' => [FilterContain::class, FilterNotContain::class],
                    'transactionStatus' => [FilterContain::class, FilterNotContain::class],
                    'supplier' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    'codeNumberOrdinal' => IntegerType::class,
                    'codeNumberMonth' => ChoiceType::class,
                    'codeNumberYear' => IntegerType::class,
                    'supplier' => EntityType::class,
                ],
                'field_value_options_list' => [
                    'codeNumberMonth' => ['choices' => array_flip(PurchaseHeader::MONTH_ROMAN_NUMERALS)],
                    'transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'supplier' => [
                        'class' => Supplier::class, 
                        'choice_label' => 'company',
                        'query_builder' => function($repository) {
                            return $repository->createQueryBuilder('e')
                                    ->andWhere("e.isInactive = false")
                                    ->addOrderBy('e.company', 'ASC');
                        },
                    ],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => [
                    'codeNumberOrdinal', 
                    'codeNumberMonth', 
                    'codeNumberYear', 
                    'transactionDate', 
                    'supplierInvoiceCodeNumber', 
                    'supplier', 
                    'invoiceTaxCodeNumber',
                    'transactionStatus'
                ],
                'field_label_list' => [
                    'codeNumberOrdinal' => '',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => 'Code Number',
                    'transactionDate' => 'Tanggal',
                    'supplierInvoiceCodeNumber' => 'Supplier Invoice #',
                    'supplier' => 'Supplier',
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'supplierInvoiceCodeNumber' => [SortAscending::class, SortDescending::class],
                    'transactionDate' => [SortAscending::class, SortDescending::class],
                    'invoiceTaxCodeNumber' => [SortAscending::class, SortDescending::class],
                    'transactionStatus' => [SortAscending::class, SortDescending::class],
                    'supplier' => [SortAscending::class, SortDescending::class],
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
