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
use App\Entity\Master\Warehouse;
use App\Entity\StockHeader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryRequestPaperDetailGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [
                    'inventoryRequestPaperHeader:codeNumberOrdinal', 
                    'inventoryRequestPaperHeader:codeNumberMonth', 
                    'inventoryRequestPaperHeader:codeNumberYear', 
                    'inventoryRequestPaperHeader:pickupDate', 
                    'inventoryRequestPaperHeader:warehouse',
                    'masterOrderHeader:codeNumberOrdinal', 
                    'masterOrderHeader:codeNumberMonth', 
                    'masterOrderHeader:codeNumberYear', 
                    'customer:company',
                    'paper:code',
                    'paper:name',
                    'quantity',
                    'memo'
                ],
                'field_label_list' => [
                    'inventoryRequestPaperHeader:codeNumberOrdinal' => 'Code Number',
                    'inventoryRequestPaperHeader:codeNumberMonth' => '',
                    'inventoryRequestPaperHeader:codeNumberYear' => '',
                    'inventoryRequestPaperHeader:pickupDate' => 'Tanggal Pakai',
                    'inventoryRequestPaperHeader:warehouse' => 'Gudang',
                ],
                'field_operators_list' => [
                    'inventoryRequestPaperHeader:codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'inventoryRequestPaperHeader:codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'inventoryRequestPaperHeader:codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'inventoryRequestPaperHeader:pickupDate' => [FilterEqual::class, FilterNotEqual::class],
                    'inventoryRequestPaperHeader:warehouse' => [FilterEqual::class, FilterNotEqual::class],
                    'masterOrderHeader:codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'masterOrderHeader:codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'masterOrderHeader:codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'customer:company' => [FilterContain::class, FilterNotContain::class],
                    'paper:code' => [FilterContain::class, FilterNotContain::class],
                    'paper:name' => [FilterContain::class, FilterNotContain::class],
                    'quantity' => [FilterEqual::class, FilterNotEqual::class],
                    'memo' => [FilterContain::class, FilterNotContain::class],
                ],
                'field_value_type_list' => [
                    'inventoryRequestPaperHeader:codeNumberOrdinal' => IntegerType::class,
                    'inventoryRequestPaperHeader:codeNumberMonth' => ChoiceType::class,
                    'inventoryRequestPaperHeader:codeNumberYear' => IntegerType::class,
                    'inventoryRequestPaperHeader:warehouse' => EntityType::class,
                ],
                'field_value_options_list' => [
                    'inventoryRequestPaperHeader:codeNumberMonth' => ['choices' => array_flip(StockHeader::MONTH_ROMAN_NUMERALS)],
                    'inventoryRequestPaperHeader:pickupDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'inventoryRequestPaperHeader:warehouse' => ['class' => Warehouse::class, 'choice_label' => 'name'],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => [
                    'inventoryRequestPaperHeader:codeNumberOrdinal', 
                    'inventoryRequestPaperHeader:codeNumberMonth', 
                    'inventoryRequestPaperHeader:codeNumberYear', 
                    'inventoryRequestPaperHeader:pickupDate', 
                    'inventoryRequestPaperHeader:warehouse',
                    'masterOrderHeader:codeNumberOrdinal', 
                    'masterOrderHeader:codeNumberMonth', 
                    'masterOrderHeader:codeNumberYear', 
                    'customer:company',
                    'paper:code',
                    'paper:name',
                    'quantity',
                    'memo'
                ],
                'field_label_list' => [
                    'inventoryRequestPaperHeader:codeNumberOrdinal' => '',
                    'inventoryRequestPaperHeader:codeNumberMonth' => '',
                    'inventoryRequestPaperHeader:codeNumberYear' => 'Code Number',
                    'inventoryRequestPaperHeader:pickupDate' => 'Tanggal Pakai',
                    'inventoryRequestPaperHeader:warehouse' => 'Gudang',
                ],
                'field_operators_list' => [
                    'inventoryRequestPaperHeader:codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'inventoryRequestPaperHeader:codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'inventoryRequestPaperHeader:codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'inventoryRequestPaperHeader:pickupDate' => [SortAscending::class, SortDescending::class],
                    'inventoryRequestPaperHeader:warehouse' => [SortAscending::class, SortDescending::class],
                    'masterOrderHeader:codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'masterOrderHeader:codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'masterOrderHeader:codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'customer:company' => [SortAscending::class, SortDescending::class],
                    'paper:code' => [SortAscending::class, SortDescending::class],
                    'paper:name' => [SortAscending::class, SortDescending::class],
                    'quantity' => [SortAscending::class, SortDescending::class],
                    'memo' => [SortAscending::class, SortDescending::class],
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
