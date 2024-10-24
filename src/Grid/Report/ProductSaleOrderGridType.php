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
use App\Entity\SaleHeader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSaleOrderGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [
                    'saleOrderHeader:codeNumberOrdinal', 
                    'saleOrderHeader:codeNumberMonth', 
                    'saleOrderHeader:codeNumberYear', 
                    'saleOrderHeader:orderReceiveDate', 
                    'saleOrderHeader:referenceNumber', 
                    'id', 
                    'saleOrderHeader:note', 
                    'saleOrderHeader:transactionStatus', 
                    'customer'
                ],
                'field_label_list' => [
                    'saleOrderHeader:codeNumberOrdinal' => 'Code Number',
                    'saleOrderHeader:codeNumberMonth' => '',
                    'saleOrderHeader:codeNumberYear' => '',
                    'saleOrderHeader:orderReceiveDate' => 'Tanggal',
                    'id' => 'Product',
                ],
                'field_operators_list' => [
                    'saleOrderHeader:codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'saleOrderHeader:codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'saleOrderHeader:codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'saleOrderHeader:orderReceiveDate' => [FilterBetween::class, FilterNotBetween::class],
                    'id' => [FilterEqual::class, FilterNotEqual::class],
                    'saleOrderHeader:referenceNumber' => [FilterContain::class, FilterNotContain::class],
                    'saleOrderHeader:note' => [FilterContain::class, FilterNotContain::class],
                    'saleOrderHeader:transactionStatus' => [FilterEqual::class, FilterNotEqual::class],
                    'customer' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    'saleOrderHeader:codeNumberOrdinal' => IntegerType::class,
                    'saleOrderHeader:codeNumberMonth' => ChoiceType::class,
                    'saleOrderHeader:codeNumberYear' => IntegerType::class,
                    'customer' => EntityType::class,
                ],
                'field_value_options_list' => [
                    'saleOrderHeader:codeNumberMonth' => ['choices' => array_flip(SaleHeader::MONTH_ROMAN_NUMERALS)],
                    'saleOrderHeader:orderReceiveDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'customer' => [
                        'class' => Customer::class, 
                        'choice_label' => 'company',
                        'query_builder' => function($repository) {
                            return $repository->createQueryBuilder('e')
                                    ->andWhere("e.isInactive = false")
                                    ->addOrderBy('e.company', 'ASC');
                        },
                    ],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => [
                    'saleOrderHeader:orderReceiveDate', 
                    'customer', 
                    'id', 
                    'saleOrderHeader:referenceNumber', 
                    'saleOrderHeader:note', 
                    'saleOrderHeader:transactionStatus', 
                    'saleOrderHeader:codeNumberYear', 
                    'saleOrderHeader:codeNumberMonth', 
                    'saleOrderHeader:codeNumberOrdinal'
                ],
                'field_label_list' => [
                    'saleOrderHeader:codeNumberOrdinal' => '',
                    'saleOrderHeader:codeNumberMonth' => '',
                    'saleOrderHeader:codeNumberYear' => 'Code Number',
                    'saleOrderHeader:orderReceiveDate' => 'Tanggal',
                    'id' => 'Product',
                ],
                'field_operators_list' => [
                    'saleOrderHeader:codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'saleOrderHeader:codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'saleOrderHeader:codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'saleOrderHeader:orderReceiveDate' => [SortAscending::class, SortDescending::class],
                    'id' => [SortAscending::class, SortDescending::class],
                    'customer' => [SortAscending::class, SortDescending::class],
                    'saleOrderHeader:referenceNumber' => [SortAscending::class, SortDescending::class],
                    'saleOrderHeader:note' => [SortAscending::class, SortDescending::class],
                    'saleOrderHeader:transactionStatus' => [SortAscending::class, SortDescending::class],
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
