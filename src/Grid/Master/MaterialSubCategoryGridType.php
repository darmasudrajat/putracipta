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
use App\Entity\Master\MaterialCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialSubCategoryGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['name', 'code', 'materialCategory', 'isInactive'],
                'field_label_list' => [
                    'code' => 'Kode',
                    'name' => 'Nama',
                    'materialCategory' => 'Category',
                    'isInactive' => 'Inactive',
                ],
                'field_operators_list' => [
                    'code' => [FilterContain::class, FilterNotContain::class],
                    'name' => [FilterContain::class, FilterNotContain::class],
                    'materialCategory' => [FilterEqual::class, FilterNotEqual::class],
                    'isInactive' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    'materialCategory' => EntityType::class,
                    'isInactive' => ChoiceType::class,
                ],
                'field_value_options_list' => [
                    'materialCategory' => ['class' => MaterialCategory::class, 'choice_label' => 'name'],
                    'isInactive' => ['choices' => ['Yes' => true, 'No' => false]],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['name', 'code', 'materialCategory:name', 'isInactive'],
                'field_label_list' => [
                    'name' => 'Nama',
                    'code' => 'Kode',
                    'materialCategory:name' => 'Category',
                    'isInactive' => 'Inactive',
                ],
                'field_operators_list' => [
                    'name' => [SortAscending::class, SortDescending::class],
                    'code' => [SortAscending::class, SortDescending::class],
                    'materialCategory:name' => [SortAscending::class, SortDescending::class],
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
