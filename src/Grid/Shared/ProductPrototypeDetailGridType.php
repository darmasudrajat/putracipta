<?php

namespace App\Grid\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterContain;
use App\Common\Data\Operator\FilterNotContain;
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\FilterType;
use App\Common\Form\Type\PaginationType;
use App\Common\Form\Type\SortType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPrototypeDetailGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['product:name', 'product:code', 'product:measurement'],
                'field_label_list' => [
                    'product:name' => 'Produk',
                    'product:code' => 'Code',
                    'product:measurement' => 'Ukuran Jadi',
                ],
                'field_operators_list' => [
                    'product:name' => [FilterContain::class, FilterNotContain::class],
                    'product:code' => [FilterContain::class, FilterNotContain::class],
                    'product:measurement' => [FilterContain::class, FilterNotContain::class],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['product:name', 'product:code', 'product:measurement'],
                'field_label_list' => [
                    'product:name' => 'Material',
                    'product:code' => 'Code',
                    'product:measurement' => 'Ukuran Jadi',
                ],
                'field_operators_list' => [
                    'product:name' => [SortAscending::class, SortDescending::class],
                    'product:code' => [SortAscending::class, SortDescending::class],
                    'product:measurement' => [SortAscending::class, SortDescending::class],
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
