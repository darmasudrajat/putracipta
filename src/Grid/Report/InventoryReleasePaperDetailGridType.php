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
use App\Entity\Stock\InventoryReleaseHeader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryReleasePaperDetailGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [
                    'inventoryReleaseHeader:transactionDate', 
                    'inventoryReleaseHeader:warehouse', 
                    'inventoryReleaseHeader:note', 
                    'inventoryReleaseHeader:releaseMode',
                    'paper:code',
                    'paper:name',
                    'memo',
                ],
                'field_label_list' => [
                    'inventoryReleaseHeader:transactionDate' => 'Tanggal',
                    'inventoryReleaseHeader:warehouse' => 'Gudang',
                    'inventoryReleaseHeader:note' => 'Note',
                    'inventoryReleaseHeader:releaseMode' => 'Jenis',
                    'paper:code' => 'Kode Paper',
                    'paper:name' => 'Nama Paper',
                ],
                'field_operators_list' => [
                    'inventoryReleaseHeader:transactionDate' => [FilterBetween::class, FilterNotBetween::class],
                    'inventoryReleaseHeader:warehouse' => [FilterEqual::class, FilterNotEqual::class],
                    'inventoryReleaseHeader:note' => [FilterContain::class, FilterNotContain::class],
                    'inventoryReleaseHeader:releaseMode' => [FilterEqual::class, FilterNotEqual::class],
                    'paper:code' => [FilterContain::class, FilterNotContain::class],
                    'paper:name' => [FilterContain::class, FilterNotContain::class],
                    'memo' => [FilterContain::class, FilterNotContain::class],
                ],
                'field_value_type_list' => [
                    'inventoryReleaseHeader:warehouse' => EntityType::class,
                    'inventoryReleaseHeader:releaseMode' => ChoiceType::class,
                ],
                'field_value_options_list' => [
                    'inventoryReleaseHeader:transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'inventoryReleaseHeader:warehouse' => ['class' => Warehouse::class, 'choice_label' => 'name'],
                    'inventoryReleaseHeader:releaseMode' => ['choices' => [
                        'Paper' => InventoryReleaseHeader::RELEASE_MODE_MATERIAL, 
                        'Paper' => InventoryReleaseHeader::RELEASE_MODE_PAPER
                    ]],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => [
                    'inventoryReleaseHeader:transactionDate', 
                    'inventoryReleaseHeader:warehouse', 
                    'inventoryReleaseHeader:note', 
                    'inventoryReleaseHeader:releaseMode',
                    'paper:code',
                    'paper:name',
                    'memo',
                ],
                'field_label_list' => [
                    'inventoryReleaseHeader:transactionDate' => 'Tanggal',
                    'inventoryReleaseHeader:warehouse' => 'Gudang',
                    'inventoryReleaseHeader:note' => 'Note',
                    'inventoryReleaseHeader:releaseMode' => 'Jenis',
                    'paper:code' => 'Kode Paper',
                    'paper:name' => 'Nama Paper',
                ],
                'field_operators_list' => [
                    'inventoryReleaseHeader:transactionDate' => [SortAscending::class, SortDescending::class],
                    'inventoryReleaseHeader:note' => [SortAscending::class, SortDescending::class],
                    'inventoryReleaseHeader:warehouse' => [SortAscending::class, SortDescending::class],
                    'inventoryReleaseHeader:releaseMode' => [SortAscending::class, SortDescending::class],
                    'paper:code' => [SortAscending::class, SortDescending::class],
                    'paper:name' => [SortAscending::class, SortDescending::class],
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
