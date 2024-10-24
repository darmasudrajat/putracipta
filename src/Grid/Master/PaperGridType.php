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
use App\Entity\Master\Paper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaperGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['name', 'code', 'type', 'materialSubCategory:name', 'weight', 'isInactive'],
                'field_label_list' => [
                    'name' => 'Nama',
                    'weight' => 'Berat (gsm)',
                    'materialSubCategory:name' => 'Jenis',
                ],
                'field_operators_list' => [
                    'name' => [FilterContain::class, FilterNotContain::class],
                    'weight' => [FilterEqual::class, FilterNotEqual::class],
                    'code' => [FilterEqual::class, FilterNotEqual::class],
                    'type' => [FilterEqual::class, FilterNotEqual::class],
                    'materialSubCategory:name' => [FilterContain::class, FilterNotContain::class],
                    'isInactive' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    'type' => ChoiceType::class,
                    'isInactive' => ChoiceType::class,
                ],
                'field_value_options_list' => [
                    'type' => ['choices' => ['000' => 'non', 'FSC' => 'fsc']],
                    'isInactive' => ['choices' => ['Inactive' => true, 'Active' => false]],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['name', 'code', 'type', 'materialSubCategory:name', 'weight', 'isInactive'],
                'field_label_list' => [
                    'name' => 'Nama',
                    'weight' => 'Berat (gsm)',
                    'materialSubCategory:name' => 'Jenis',
                ],
                'field_operators_list' => [
                    'name' => [SortAscending::class, SortDescending::class],
                    'materialSubCategory:name' => [SortAscending::class, SortDescending::class],
                    'weight' => [SortAscending::class, SortDescending::class],
                    'code' => [SortAscending::class, SortDescending::class],
                    'type' => [SortAscending::class, SortDescending::class],
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
