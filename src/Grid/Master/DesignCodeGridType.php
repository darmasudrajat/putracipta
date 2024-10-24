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

class DesignCodeGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [ 'code', 'variant', 'version', 'name', 'customer:company', 'note', 'status', 'isInactive'],
                'field_label_list' => [
                    'code' => 'Code',
                    'variant' => '',
                    'version' => '',
                    'customer:company' => 'Customer',
                ],
                'field_operators_list' => [
                    'isInactive' => [FilterEqual::class, FilterNotEqual::class],
                    'status' => [FilterEqual::class, FilterNotEqual::class],
                    'name' => [FilterContain::class, FilterNotContain::class],
                    'code' => [FilterContain::class, FilterNotContain::class],
                    'variant' => [FilterContain::class, FilterNotContain::class],
                    'version' => [FilterContain::class, FilterNotContain::class],
                    'note' => [FilterContain::class, FilterNotContain::class],
                    'customer:company' => [FilterContain::class, FilterNotContain::class],
                ],
                'field_value_type_list' => [
                    'status' => ChoiceType::class,
                    'isInactive' => ChoiceType::class,
                ],
                'field_value_options_list' => [
                    'isInactive' => ['choices' => ['Inactive' => true, 'Active' => false]],
                    'status' => ['choices' => ['FA' => 'fa', 'NA' => 'na']],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['name', 'code', 'variant', 'version', 'customer:company', 'note', 'status', 'isInactive'],
                'field_label_list' => [
                    'code' => '',
                    'variant' => '',
                    'version' => 'Code',
                    'customer:company' => 'Customer',
                ],
                'field_operators_list' => [
                    'name' => [SortAscending::class, SortDescending::class],
                    'code' => [SortAscending::class, SortDescending::class],
                    'variant' => [SortAscending::class, SortDescending::class],
                    'version' => [SortAscending::class, SortDescending::class],
                    'status' => [SortAscending::class, SortDescending::class],
                    'note' => [SortAscending::class, SortDescending::class],
                    'customer:company' => [SortAscending::class, SortDescending::class],
                    'isInactive' => [SortAscending::class, SortDescending::class],
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
