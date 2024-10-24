<?php

namespace App\Grid\Master;

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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DielineMillarGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['note', 'version', 'isInactive', 'name', 'code', 'customer:company', 'printingLayout'],
                'field_label_list' => [
                    'code' => 'Code',
                    'version' => '',
                    'printingLayout' => 'Kris Layout Cetak',
                    'customer:company' => 'Customer',
                ],
                'field_operators_list' => [
                    'note' => [FilterContain::class, FilterNotContain::class],
                    'version' => [FilterContain::class, FilterNotContain::class],
                    'isInactive' => [FilterEqual::class, FilterNotEqual::class],
                    'name' => [FilterContain::class, FilterNotContain::class],
                    'code' => [FilterContain::class, FilterNotContain::class],
                    'printingLayout' => [FilterContain::class, FilterNotContain::class],
                    'customer:company' => [FilterContain::class, FilterNotContain::class],
                ],
                'field_value_type_list' => [
                    'isInactive' => ChoiceType::class,
                ],
                'field_value_options_list' => [
                    'isInactive' => ['choices' => ['Inactive' => true, 'Active' => false]],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['note', 'version', 'isInactive', 'name', 'code', 'customer:company', 'printingLayout'],
                'field_label_list' => [
                    'code' => '',
                    'version' => 'Code',
                    'printingLayout' => 'Kris Layout Cetak',
                    'customer:company' => 'Customer',
                ],
                'field_operators_list' => [
                    'note' => [SortAscending::class, SortDescending::class],
                    'version' => [SortAscending::class, SortDescending::class],
                    'isInactive' => [SortAscending::class, SortDescending::class],
                    'name' => [SortAscending::class, SortDescending::class],
                    'code' => [SortAscending::class, SortDescending::class],
                    'printingLayout' => [SortAscending::class, SortDescending::class],
                    'customer:company' => [SortAscending::class, SortDescending::class],
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
