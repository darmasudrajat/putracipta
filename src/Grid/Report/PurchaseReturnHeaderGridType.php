<?php

namespace App\Grid\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Common\Data\Operator\FilterContain;
use App\Common\Data\Operator\FilterEqual;
use App\Common\Data\Operator\FilterNotBetween;
use App\Common\Data\Operator\FilterNotContain;
use App\Common\Data\Operator\FilterNotEqual;
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\FilterType;
use App\Common\Form\Type\PaginationType;
use App\Common\Form\Type\SortType;
use App\Entity\Master\Warehouse;
use App\Entity\PurchaseHeader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseReturnHeaderGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'transactionDate', 'supplier:company', 'receiveHeaderCodeNumberOrdinal', 'receiveHeaderCodeNumberMonth', 'receiveHeaderCodeNumberYear', 'warehouse'],
                'field_label_list' => [
                    'codeNumberOrdinal' => 'Code Number',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => '',
                    'transactionDate' => 'Tanggal',
                    'supplier:company' => 'Supplier',
                    'receiveHeaderCodeNumberOrdinal' => 'Receive Number',
                    'receiveHeaderCodeNumberMonth' => '',
                    'receiveHeaderCodeNumberYear' => '',
                    'warehouse' => 'Gudang',
                ],
                'field_operators_list' => [
                    'grandTotal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'receiveHeaderCodeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'receiveHeaderCodeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'receiveHeaderCodeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'transactionDate' => [FilterBetween::class, FilterNotBetween::class],
                    'supplier:company' => [FilterContain::class, FilterNotContain::class],
                    'warehouse' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    'codeNumberOrdinal' => IntegerType::class,
                    'codeNumberMonth' => ChoiceType::class,
                    'codeNumberYear' => IntegerType::class,
                    'receiveHeaderCodeNumberOrdinal' => IntegerType::class,
                    'receiveHeaderCodeNumberMonth' => ChoiceType::class,
                    'receiveHeaderCodeNumberYear' => IntegerType::class,
                    'warehouse' => EntityType::class,
                ],
                'field_value_options_list' => [
                    'codeNumberMonth' => ['choices' => array_flip(PurchaseHeader::MONTH_ROMAN_NUMERALS)],
                    'transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'warehouse' => ['class' => Warehouse::class, 'choice_label' => 'name'],
                ],  
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['transactionDate', 'supplier:company', 'warehouse', 'receiveHeaderCodeNumberYear', 'receiveHeaderCodeNumberMonth', 'receiveHeaderCodeNumberOrdinal', 'codeNumberYear', 'codeNumberMonth', 'codeNumberOrdinal'],
                'field_label_list' => [
                    'codeNumberOrdinal' => '',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => 'Code Number',
                    'receiveHeaderCodeNumberOrdinal' => '',
                    'receiveHeaderCodeNumberMonth' => '',
                    'receiveHeaderCodeNumberYear' => 'PO #',
                    'transactionDate' => 'Tanggal',
                    'supplier:company' => 'Supplier',
                    'warehouse' => 'Gudang',
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'transactionDate' => [SortAscending::class, SortDescending::class],
                    'supplier:company' => [SortAscending::class, SortDescending::class],
                    'receiveHeaderCodeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'receiveHeaderCodeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'receiveHeaderCodeNumberYear' => [SortAscending::class, SortDescending::class],
                    'warehouse' => [SortAscending::class, SortDescending::class],
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
