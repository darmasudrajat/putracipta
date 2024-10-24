<?php

namespace App\Grid\Stock;

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
use App\Entity\Master\Warehouse;
use App\Entity\ProductionHeader;
use App\Entity\StockHeader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryProductReceiveHeaderGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'transactionDate', 'masterOrderHeader:codeNumberOrdinal', 'masterOrderHeader:codeNumberMonth', 'masterOrderHeader:codeNumberYear', 'customer:company', 'warehouse', 'productDetailLists', 'productCodeLists'],
                'field_label_list' => [
                    'codeNumberOrdinal' => 'Code Number',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => '',
                    'masterOrderHeader:codeNumberOrdinal' => 'Master Order Number',
                    'masterOrderHeader:codeNumberMonth' => '',
                    'masterOrderHeader:codeNumberYear' => '',
                    'customer:company' => 'Customer',
                    'transactionDate' => 'Tanggal',
                    'warehouse' => 'Gudang',
                    'productDetailLists' => 'Products',
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'transactionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'masterOrderHeader:codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'masterOrderHeader:codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'masterOrderHeader:codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'customer:company' => [FilterContain::class, FilterNotContain::class],
                    'warehouse' => [FilterEqual::class, FilterNotEqual::class],
                    'productDetailLists' => [FilterContain::class, FilterNotContain::class],
                    'productCodeLists' => [FilterContain::class, FilterNotContain::class],
                ],
                'field_value_type_list' => [
                    'codeNumberOrdinal' => IntegerType::class,
                    'codeNumberMonth' => ChoiceType::class,
                    'codeNumberYear' => IntegerType::class,
                    'masterOrderHeader:codeNumberOrdinal' => IntegerType::class,
                    'masterOrderHeader:codeNumberMonth' => ChoiceType::class,
                    'masterOrderHeader:codeNumberYear' => IntegerType::class,
                    'warehouse' => EntityType::class,
                ],
                'field_value_options_list' => [
                    'codeNumberMonth' => ['choices' => array_flip(StockHeader::MONTH_ROMAN_NUMERALS)],
                    'masterOrderHeader:codeNumberMonth' => ['choices' => array_flip(ProductionHeader::MONTH_ROMAN_NUMERALS)],
                    'transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'warehouse' => ['class' => Warehouse::class, 'choice_label' => 'name'],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['transactionDate', 'productDetailLists', 'warehouse', 'note', 'productCodeLists', 'id'],
                'field_label_list' => [
                    'id' => 'Code Number',
                    'transactionDate' => 'Tanggal',
                    'warehouse' => 'Gudang',
                ],
                'field_operators_list' => [
                    'id' => [SortAscending::class, SortDescending::class],
                    'transactionDate' => [SortAscending::class, SortDescending::class],
                    'warehouse' => [SortAscending::class, SortDescending::class],
                    'note' => [SortAscending::class, SortDescending::class],
                    'productDetailLists' => [SortAscending::class, SortDescending::class],
                    'productCodeLists' => [SortAscending::class, SortDescending::class],
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
