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
use App\Entity\Master\MaterialCategory;
use App\Entity\Master\MaterialSubCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['name', 'code', 'materialSubCategory', 'materialSubCategory:materialCategory'],
                'field_label_list' => [
                    'name' => 'Nama',
                    'code' => 'Code',
                    'materialSubCategory:materialCategory' => 'Category',
                    'materialSubCategory' => 'Sub Category',
                ],
                'field_operators_list' => [
                    'name' => [FilterContain::class, FilterNotContain::class],
                    'code' => [FilterContain::class, FilterNotContain::class],
                    'materialSubCategory:materialCategory' => [FilterEqual::class, FilterNotEqual::class],
                    'materialSubCategory' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    'materialSubCategory:materialCategory' => EntityType::class,
                    'materialSubCategory' => EntityType::class,
                ],
                'field_value_options_list' => [
                    'materialSubCategory:materialCategory' => ['class' => MaterialCategory::class, 'choice_label' => 'name'],
                    'materialSubCategory' => ['class' => MaterialSubCategory::class, 'choice_label' => 'name'],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['name', 'code', 'materialCategory:name', 'materialSubCategory:name'],
                'field_label_list' => [
                    'name' => 'Nama',
                    'code' => 'Code',
                    'materialCategory:name' => 'Category',
                    'materialSubCategory:name' => 'Sub Category',
                ],
                'field_operators_list' => [
                    'name' => [SortAscending::class, SortDescending::class],
                    'code' => [SortAscending::class, SortDescending::class],
                    'materialCategory:name' => [SortAscending::class, SortDescending::class],
                    'materialSubCategory:name' => [SortAscending::class, SortDescending::class],
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
