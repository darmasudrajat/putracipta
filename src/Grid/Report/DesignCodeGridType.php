<?php

namespace App\Grid\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Common\Data\Operator\FilterContain;
use App\Common\Data\Operator\FilterNotBetween;
use App\Common\Data\Operator\FilterNotContain;
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\FilterType;
use App\Common\Form\Type\PaginationType;
use App\Common\Form\Type\SortType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DesignCodeGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [ 'code', 'variant', 'version', 'name', 'createdTransactionDateTime', 'customer:company'],
                'field_label_list' => [
                    'code' => 'Code',
                    'variant' => '',
                    'version' => '',
                    'customer:company' => 'Customer',
                    'createdTransactionDateTime' => 'Tanggal',
                ],
                'field_operators_list' => [
                    'name' => [FilterContain::class, FilterNotContain::class],
                    'code' => [FilterContain::class, FilterNotContain::class],
                    'variant' => [FilterContain::class, FilterNotContain::class],
                    'version' => [FilterContain::class, FilterNotContain::class],
                    'customer:company' => [FilterContain::class, FilterNotContain::class],
                    'createdTransactionDateTime' => [FilterBetween::class, FilterNotBetween::class],
                ],
                'field_value_options_list' => [
                    'createdTransactionDateTime' => ['attr' => ['data-controller' => 'flatpickr-element']],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['name', 'code', 'variant', 'version', 'customer:company', 'createdTransactionDateTime'],
                'field_label_list' => [
                    'code' => '',
                    'variant' => '',
                    'version' => 'Code',
                    'customer:company' => 'Customer',
                    'createdTransactionDateTime' => 'Tanggal',
                ],
                'field_operators_list' => [
                    'name' => [SortAscending::class, SortDescending::class],
                    'code' => [SortAscending::class, SortDescending::class],
                    'variant' => [SortAscending::class, SortDescending::class],
                    'version' => [SortAscending::class, SortDescending::class],
                    'customer:company' => [SortAscending::class, SortDescending::class],
                    'createdTransactionDateTime' => [SortAscending::class, SortDescending::class],
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
