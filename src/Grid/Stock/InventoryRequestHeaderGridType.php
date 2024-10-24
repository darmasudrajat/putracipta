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
use App\Entity\Master\Division;
use App\Entity\Master\Warehouse;
use App\Entity\StockHeader;
use App\Entity\Stock\InventoryRequestHeader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryRequestHeaderGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [
                    'division', 
                    'codeNumberOrdinal', 
                    'codeNumberMonth', 
                    'codeNumberYear', 
                    'transactionDate', 
                    'requestMode', 
                    'warehouse', 
                    'requestStatus', 
                    'inventoryRequestProductList',
                    'note'
                ],
                'field_label_list' => [
                    'codeNumberOrdinal' => 'Code Number',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => '',
                    'transactionDate' => 'Tanggal',
                    'warehouse' => 'Gudang',
                    'division' => 'Divisi',
                ],
                'field_operators_list' => [
                    'inventoryRequestProductList' => [FilterContain::class, FilterNotContain::class],
                    'departmentName' => [FilterContain::class, FilterNotContain::class],
                    'note' => [FilterContain::class, FilterNotContain::class],
                    'codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'transactionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'requestMode' => [FilterEqual::class, FilterNotEqual::class],
                    'warehouse' => [FilterEqual::class, FilterNotEqual::class],
                    'division' => [FilterEqual::class, FilterNotEqual::class],
                    'requestStatus' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    'codeNumberOrdinal' => IntegerType::class,
                    'codeNumberMonth' => ChoiceType::class,
                    'codeNumberYear' => IntegerType::class,
                    'warehouse' => EntityType::class,
                    'division' => EntityType::class,
                    'requestMode' => ChoiceType::class,
                    'requestStatus' => ChoiceType::class,
                ],
                'field_value_options_list' => [
                    'codeNumberMonth' => ['choices' => array_flip(StockHeader::MONTH_ROMAN_NUMERALS)],
                    'transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'warehouse' => ['class' => Warehouse::class, 'choice_label' => 'name'],
                    'division' => ['class' => Division::class, 'choice_label' => 'name'],
                    'requestMode' => ['choices' => [
                        'Material' => InventoryRequestHeader::REQUEST_MODE_MATERIAL, 
                        'Paper' => InventoryRequestHeader::REQUEST_MODE_PAPER
                    ]],
                    'requestStatus' => ['choices' => [
                        'Open' => InventoryRequestHeader::REQUEST_STATUS_OPEN, 
                        'Close' => InventoryRequestHeader::REQUEST_STATUS_CLOSE, 
                        'Partial' => InventoryRequestHeader::REQUEST_STATUS_PARTIAL
                    ]],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => [
                    'division', 
                    'codeNumberOrdinal', 
                    'codeNumberMonth', 
                    'codeNumberYear', 
                    'transactionDate', 
                    'requestMode', 
                    'warehouse', 
                    'requestStatus', 
                    'inventoryRequestProductList',
                    'note'
                ],
                'field_label_list' => [
                    'codeNumberOrdinal' => '',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => 'Code Number',
                    'transactionDate' => 'Tanggal',
                    'warehouse' => 'Gudang',
                    'division' => 'Divisi',
                ],
                'field_operators_list' => [
                    'departmentName' => [SortAscending::class, SortDescending::class],
                    'inventoryRequestProductList' => [SortAscending::class, SortDescending::class],
                    'codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'transactionDate' => [SortAscending::class, SortDescending::class],
                    'requestMode' => [SortAscending::class, SortDescending::class],
                    'warehouse' => [SortAscending::class, SortDescending::class],
                    'division' => [SortAscending::class, SortDescending::class],
                    'requestStatus' => [SortAscending::class, SortDescending::class],
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
