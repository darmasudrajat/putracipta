<?php

namespace App\Grid\Accounting;

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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountingLedgerGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['transactionType', 'transactionSubject', 'debitAmount', 'creditAmount'],
                'field_operators_list' => [
                    'transactionType' => [FilterContain::class, FilterNotContain::class],
                    'transactionSubject' => [FilterContain::class, FilterNotContain::class],
                    'debitAmount' => [FilterEqual::class, FilterNotEqual::class],
                    'creditAmount' => [FilterEqual::class, FilterNotEqual::class],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['transactionType', 'transactionSubject', 'debitAmount', 'creditAmount'],
                'field_operators_list' => [
                    'transactionType' => [SortAscending::class, SortDescending::class],
                    'transactionSubject' => [SortAscending::class, SortDescending::class],
                    'debitAmount' => [SortAscending::class, SortDescending::class],
                    'creditAmount' => [SortAscending::class, SortDescending::class],
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
