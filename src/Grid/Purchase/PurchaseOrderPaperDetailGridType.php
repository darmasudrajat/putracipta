<?php

namespace App\Grid\Purchase;

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
use App\Entity\PurchaseHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseOrderPaperDetailGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [
                    'purchaseOrderPaperHeader:codeNumberOrdinal', 
                    'purchaseOrderPaperHeader:codeNumberMonth', 
                    'purchaseOrderPaperHeader:codeNumberYear', 
                    'purchaseOrderPaperHeader:transactionDate', 
                    'supplier:company', 
                    'paper:code', 
                    'paper:name', 
                    'quantity',
                    'purchaseOrderPaperHeader:transactionStatus'
                ],
                'field_label_list' => [
                    'purchaseOrderPaperHeader:codeNumberOrdinal' => 'Code Number',
                    'purchaseOrderPaperHeader:codeNumberMonth' => '',
                    'purchaseOrderPaperHeader:codeNumberYear' => '',
                    'purchaseOrderPaperHeader:transactionDate' => 'Tanggal',
                    'supplier:company' => 'Supplier',
                    'purchaseOrderPaperHeader:transactionStatus' => 'Status',
                ],
                'field_operators_list' => [
                    'purchaseOrderPaperHeader:codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderPaperHeader:codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderPaperHeader:codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderPaperHeader:transactionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'supplier:company' => [FilterContain::class, FilterNotContain::class],
                    'paper:code' => [FilterContain::class, FilterNotContain::class],
                    'paper:name' => [FilterContain::class, FilterNotContain::class],
                    'quantity' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderPaperHeader:transactionStatus' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    'purchaseOrderPaperHeader:codeNumberOrdinal' => IntegerType::class,
                    'purchaseOrderPaperHeader:codeNumberMonth' => ChoiceType::class,
                    'purchaseOrderPaperHeader:codeNumberYear' => IntegerType::class,
                ],
                'field_value_options_list' => [
                    'purchaseOrderPaperHeader:codeNumberMonth' => ['choices' => array_flip(PurchaseHeader::MONTH_ROMAN_NUMERALS)],
                    'purchaseOrderPaperHeader:transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => [
                    'purchaseOrderPaperHeader:transactionDate', 
                    'supplier:company', 
                    'paper:code', 
                    'paper:name', 
                    'quantity', 
                    'purchaseOrderPaperHeader:transactionStatus', 
                    'purchaseOrderPaperHeader:codeNumberYear', 
                    'purchaseOrderPaperHeader:codeNumberMonth', 
                    'purchaseOrderPaperHeader:codeNumberOrdinal'
                ],
                'field_label_list' => [
                    'purchaseOrderPaperHeader:codeNumberOrdinal' => 'Code Number',
                    'purchaseOrderPaperHeader:codeNumberMonth' => '',
                    'purchaseOrderPaperHeader:codeNumberYear' => '',
                    'purchaseOrderPaperHeader:transactionDate' => 'Tanggal',
                    'supplier:company' => 'Supplier',
                    'purchaseOrderPaperHeader:transactionStatus' => 'Status',
                ],
                'field_operators_list' => [
                    'purchaseOrderPaperHeader:codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderPaperHeader:codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderPaperHeader:codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderPaperHeader:transactionDate' => [SortAscending::class, SortDescending::class],
                    'supplier:company' => [SortAscending::class, SortDescending::class],
                    'quantity' => [SortAscending::class, SortDescending::class],
                    'paper:code' => [SortAscending::class, SortDescending::class],
                    'paper:name' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderPaperHeader:transactionStatus' => [SortAscending::class, SortDescending::class],
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
