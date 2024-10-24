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
use App\Entity\Master\Customer;
use App\Entity\Master\Employee;
use App\Entity\Sale\SaleOrderHeader;
use App\Entity\SaleHeader;
use App\Repository\Master\DivisionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleOrderHeaderGridType extends AbstractType
{
    private DivisionRepository $divisionRepository;

    public function __construct(DivisionRepository $divisionRepository)
    {
        $this->divisionRepository = $divisionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'orderReceiveDate', 'referenceNumber', 'employee', 'customer', 'transactionStatus'],
                'field_label_list' => [
                    'codeNumberOrdinal' => 'Code Number',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => '',
                    'orderReceiveDate' => 'Tanggal',
                    'customer' => 'Customer',
                    'employee' => 'Marketing',
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'orderReceiveDate' => [FilterBetween::class, FilterNotBetween::class],
                    'customer' => [FilterEqual::class, FilterNotEqual::class],
                    'employee' => [FilterEqual::class, FilterNotEqual::class],
                    'referenceNumber' => [FilterContain::class, FilterNotContain::class],
                    'transactionStatus' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    'codeNumberOrdinal' => IntegerType::class,
                    'codeNumberMonth' => ChoiceType::class,
                    'codeNumberYear' => IntegerType::class,
                    'customer' => EntityType::class,
                    'employee' => EntityType::class,
                    'transactionStatus' => ChoiceType::class,
                ],
                'field_value_options_list' => [
                    'codeNumberMonth' => ['choices' => array_flip(SaleHeader::MONTH_ROMAN_NUMERALS)],
                    'orderReceiveDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'customer' => [
                        'class' => Customer::class, 
                        'choice_label' => 'company',
                        'query_builder' => function($repository) {
                            return $repository->createQueryBuilder('e')
                                    ->andWhere("e.isInactive = false")
                                    ->addOrderBy('e.company', 'ASC');
                        },
                    ],
                    'employee' => [
                        'class' => Employee::class, 
                        'choice_label' => 'name',
                        'query_builder' => function($repository) {
                            return $repository->createQueryBuilder('e')
                                ->andWhere("e.division = :division")->setParameter('division', $this->divisionRepository->findMarketingRecord())
                                ->andWhere("e.isInactive = false");
                        },
                    ],
                    'transactionStatus' => ['choices' => [
                        'Approved' => SaleOrderHeader::TRANSACTION_STATUS_APPROVE, 
                        'Reject' => SaleOrderHeader::TRANSACTION_STATUS_REJECT,
                        'Draft' => SaleOrderHeader::TRANSACTION_STATUS_DRAFT,
                        'Complete Delivery' => SaleOrderHeader::TRANSACTION_STATUS_FULL_DELIVERY,
                        'Partial Delivery' => SaleOrderHeader::TRANSACTION_STATUS_PARTIAL_DELIVERY,
                        'Completed' => SaleOrderHeader::TRANSACTION_STATUS_DONE,
                    ]],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['orderReceiveDate', 'customer', 'employee', 'referenceNumber', 'transactionStatus', 'codeNumberYear', 'codeNumberMonth', 'codeNumberOrdinal'],
                'field_label_list' => [
                    'codeNumberOrdinal' => '',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => 'Code Number',
                    'orderReceiveDate' => 'Tanggal',
                    'customer' => 'Customer',
                    'employee' => 'Marketing',
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'orderReceiveDate' => [SortAscending::class, SortDescending::class],
                    'customer' => [SortAscending::class, SortDescending::class],
                    'employee' => [SortAscending::class, SortDescending::class],
                    'referenceNumber' => [SortAscending::class, SortDescending::class],
                    'transactionStatus' => [SortAscending::class, SortDescending::class],
                ],
            ])
            ->add('pagination', PaginationType::class, ['size_choices' => [50, 100, 300, 500]])
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
