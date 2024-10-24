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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryRequestMaterialDetailGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [
                    'inventoryRequestHeader:transactionDate', 
                    'inventoryRequestHeader:warehouse', 
                    'inventoryRequestHeader:note', 
                    'material:code',
                    'material:name',
                    'memo',
                ],
                'field_label_list' => [
                    'inventoryRequestHeader:transactionDate' => 'Tanggal',
                    'inventoryRequestHeader:warehouse' => 'Gudang',
                    'inventoryRequestHeader:note' => 'Note',
                    'material:code' => 'Kode Material',
                    'material:name' => 'Nama Material',
                ],
                'field_operators_list' => [
                    'inventoryRequestHeader:transactionDate' => [FilterBetween::class, FilterNotBetween::class],
                    'inventoryRequestHeader:warehouse' => [FilterEqual::class, FilterNotEqual::class],
                    'inventoryRequestHeader:note' => [FilterContain::class, FilterNotContain::class],
                    'material:code' => [FilterContain::class, FilterNotContain::class],
                    'material:name' => [FilterContain::class, FilterNotContain::class],
                    'memo' => [FilterContain::class, FilterNotContain::class],
                ],
                'field_value_type_list' => [
                    'inventoryRequestHeader:warehouse' => EntityType::class,
                ],
                'field_value_options_list' => [
                    'inventoryRequestHeader:transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'inventoryRequestHeader:warehouse' => ['class' => Warehouse::class, 'choice_label' => 'name'],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => [
                    'inventoryRequestHeader:transactionDate', 
                    'inventoryRequestHeader:warehouse', 
                    'inventoryRequestHeader:note', 
                    'material:code',
                    'material:name',
                    'memo',
                ],
                'field_label_list' => [
                    'inventoryRequestHeader:transactionDate' => 'Tanggal',
                    'inventoryRequestHeader:warehouse' => 'Gudang',
                    'inventoryRequestHeader:note' => 'Note',
                    'material:code' => 'Kode Material',
                    'material:name' => 'Nama Material',
                ],
                'field_operators_list' => [
                    'inventoryRequestHeader:transactionDate' => [SortAscending::class, SortDescending::class],
                    'inventoryRequestHeader:note' => [SortAscending::class, SortDescending::class],
                    'inventoryRequestHeader:warehouse' => [SortAscending::class, SortDescending::class],
                    'material:code' => [SortAscending::class, SortDescending::class],
                    'material:name' => [SortAscending::class, SortDescending::class],
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
