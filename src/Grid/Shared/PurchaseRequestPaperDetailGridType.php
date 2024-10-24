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

class PurchaseRequestPaperDetailGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['quantity', 'paper:name', 'paper:length', 'paper:width', 'paper:weight', 'unit:name', 'usageDate', 'memo'],
                'field_label_list' => [
                    'quantity' => 'Qty',
                    'usageDate' => 'Tgl Pakai',
                    'unit:name' => 'Satuan',
                    'paper:name' => 'Kertas',
                    'paper:length' => 'P',
                    'paper:width' => 'L',
                    'paper:weight' => 'GSM',
                ],
                'field_operators_list' => [
                    'length' => [FilterEqual::class, FilterNotEqual::class],
                    'width' => [FilterEqual::class, FilterNotEqual::class],
                    'quantity' => [FilterEqual::class, FilterNotEqual::class],
                    'quantity' => [FilterEqual::class, FilterNotEqual::class],
                    'usageDate' => [FilterEqual::class, FilterNotEqual::class],
                    'memo' => [FilterContain::class, FilterNotContain::class],
                    'paper:name' => [FilterContain::class, FilterNotContain::class],
                    'paper:length' => [FilterEqual::class, FilterNotEqual::class],
                    'paper:width' => [FilterEqual::class, FilterNotEqual::class],
                    'paper:weight' => [FilterEqual::class, FilterNotEqual::class],
                    'unit:name' => [FilterContain::class, FilterNotContain::class],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['paper:name', 'paper:length', 'paper:width', 'paper:weight', 'unit:name', 'quantity', 'usageDate', 'memo'],
                'field_label_list' => [
                    'quantity' => 'Qty',
                    'usageDate' => 'Tgl Pakai',
                    'unit:name' => 'Satuan',
                    'paper:name' => 'Kertas',
                    'paper:length' => 'P',
                    'paper:width' => 'L',
                    'paper:weight' => 'GSM',
                ],
                'field_operators_list' => [
                    'length' => [SortAscending::class, SortDescending::class],
                    'width' => [SortAscending::class, SortDescending::class],
                    'weight' => [SortAscending::class, SortDescending::class],
                    'quantity' => [SortAscending::class, SortDescending::class],
                    'usageDate' => [SortAscending::class, SortDescending::class],
                    'memo' => [SortAscending::class, SortDescending::class],
                    'paper:name' => [SortAscending::class, SortDescending::class],
                    'paper:length' => [SortAscending::class, SortDescending::class],
                    'paper:width' => [SortAscending::class, SortDescending::class],
                    'paper:weight' => [SortAscending::class, SortDescending::class],
                    'unit:name' => [SortAscending::class, SortDescending::class],
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
