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

class EmployeeGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['name', 'email', 'phone', 'isInactive'],
                'field_operators_list' => [
                    'name' => [FilterContain::class, FilterNotContain::class],
                    'email' => [FilterContain::class, FilterNotContain::class],
                    'phone' => [FilterContain::class, FilterNotContain::class],
                    'isInactive' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    'isInactive' => ChoiceType::class,
                ],
                'field_value_options_list' => [
                    'isInactive' => ['choices' => ['Inactive' => true, 'Active' => false]],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['name', 'email', 'phone', 'isInactive'],
                'field_operators_list' => [
                    'name' => [SortAscending::class, SortDescending::class],
                    'email' => [SortAscending::class, SortDescending::class],
                    'phone' => [SortAscending::class, SortDescending::class],
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
