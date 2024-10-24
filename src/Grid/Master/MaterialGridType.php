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
use App\Entity\Master\MaterialSubCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['name', 'codeOrdinal', 'materialSubCategory', 'materialSubCategory:materialCategory', 'isInactive'],
                'field_label_list' => [
                    'name' => 'Nama',
                    'codeOrdinal' => 'Code',
                    'materialSubCategory:materialCategory' => 'Category',
                    'materialSubCategory' => 'Sub Category',
                    'isInactive' => 'Inactive',
                ],
                'field_operators_list' => [
                    'name' => [FilterContain::class, FilterNotContain::class],
                    'codeOrdinal' => [FilterContain::class, FilterNotContain::class],
                    'materialSubCategory:materialCategory' => [FilterEqual::class, FilterNotEqual::class],
                    'materialSubCategory' => [FilterEqual::class, FilterNotEqual::class],
                    'isInactive' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    'materialSubCategory:materialCategory' => EntityType::class,
                    'materialSubCategory' => EntityType::class,
                    'isInactive' => ChoiceType::class,
                ],
                'field_value_options_list' => [
                    'materialSubCategory:materialCategory' => [
                        'class' => MaterialCategory::class, 
                        'choice_label' => 'name',
                        'query_builder' => function($repository) {
                            return $repository->createQueryBuilder('e')
                                    ->andWhere("e.isInactive = false")
                                    ->addOrderBy('e.name', 'ASC');
                        },
                    ],
                    'materialSubCategory' => [
                        'class' => MaterialSubCategory::class, 
                        'choice_label' => 'name',
                        'query_builder' => function($repository) {
                            return $repository->createQueryBuilder('e')
                                    ->andWhere("IDENTITY(e.materialCategory) <> 1")
                                    ->andWhere("e.isInactive = false")
                                    ->addOrderBy('e.name', 'ASC');
                        },
                    ],
                    'isInactive' => ['choices' => ['Inactive' => true, 'Active' => false]],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['name', 'codeOrdinal', 'materialCategory:name', 'materialSubCategory:name', 'isInactive'],
                'field_label_list' => [
                    'name' => 'Nama',
                    'codeOrdinal' => 'Code',
                    'materialCategory:name' => 'Category',
                    'materialSubCategory:name' => 'Sub Category',
                    'isInactive' => 'Inactive',
                ],
                'field_operators_list' => [
                    'name' => [SortAscending::class, SortDescending::class],
                    'codeOrdinal' => [SortAscending::class, SortDescending::class],
                    'materialCategory:name' => [SortAscending::class, SortDescending::class],
                    'materialSubCategory:name' => [SortAscending::class, SortDescending::class],
                    'isInactive' => [SortAscending::class, SortDescending::class],
                ],
            ])
            ->add('pagination', PaginationType::class, ['size_choices' => [100, 300, 500]])
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
