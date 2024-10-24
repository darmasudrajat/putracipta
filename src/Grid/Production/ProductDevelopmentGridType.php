<?php

namespace App\Grid\Production;

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

class ProductDevelopmentGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['developmentType', 'epArtworkFileDate', 'epArtWorkFileTime', 'epCustomerReviewDate', 'epCustomerReviewTime', 'epSubconDiecutDate', 'epSubConDiecutTime', 'epDielineDevelopmentDate', 'epDielineDevelopmentTime', 'epImageDeliveryProductionDate', 'epImageDeliveryProductionTime', 'epDiecutDeliveryProductionDate', 'epDiecutDeliveryProductionTime', 'epDielineDeliveryProductionDate', 'epDielineDeliveryProductionTime', 'fepArtworkFileDate', 'fepArtworkFileTime', 'fepCustomerReviewDate', 'fepCustomerReviewTime', 'fepSubconDiecutDate', 'fepSubconDiecutTime', 'fepDielineDevelopmentDate', 'fepDielienDevelopmentTime', 'fepImageDeliveryProductionDate', 'fepImageDeliveryProductionTime', 'fepDiecutDeliveryProductionDate', 'fepDiecutDeliveryProductionTime', 'fepDielineDeliveryProductionDate', 'fepDielineDeliveryProductionTime', 'psArtworkFileDate', 'psArtworkFileTime', 'psCustomerReviewDate', 'psCustomerReviewTime', 'psSubconDiecutDate', 'psSubconDiecutTime', 'psDielineDevelopmentDate', 'psDielieneDevelopmentTime', 'psDielineDevelopmentTime', 'psImageDeliveryProductionDate', 'psImageDeliveryProductionTime', 'psDiecutDeliveryProductionDate', 'psDiecutDeliveryProductionTime', 'psDielineDeliveryProductionDate', 'psDielineDeliveryProductionTime', 'isCanceled', 'isRead', 'codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'createdProductionDateTime', 'modifiedProductionDateTime', 'transactionDate', 'note'],
                'field_operators_list' => [
                    'developmentType' => [FilterEqual::class, FilterNotEqual::class],
                    'isCanceled' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'transactionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'note' => [FilterContain::class, FilterNotContain::class],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['developmentType', 'epArtworkFileDate', 'epArtWorkFileTime', 'epCustomerReviewDate', 'epCustomerReviewTime', 'epSubconDiecutDate', 'epSubConDiecutTime', 'epDielineDevelopmentDate', 'epDielineDevelopmentTime', 'epImageDeliveryProductionDate', 'epImageDeliveryProductionTime', 'epDiecutDeliveryProductionDate', 'epDiecutDeliveryProductionTime', 'epDielineDeliveryProductionDate', 'epDielineDeliveryProductionTime', 'fepArtworkFileDate', 'fepArtworkFileTime', 'fepCustomerReviewDate', 'fepCustomerReviewTime', 'fepSubconDiecutDate', 'fepSubconDiecutTime', 'fepDielineDevelopmentDate', 'fepDielienDevelopmentTime', 'fepImageDeliveryProductionDate', 'fepImageDeliveryProductionTime', 'fepDiecutDeliveryProductionDate', 'fepDiecutDeliveryProductionTime', 'fepDielineDeliveryProductionDate', 'fepDielineDeliveryProductionTime', 'psArtworkFileDate', 'psArtworkFileTime', 'psCustomerReviewDate', 'psCustomerReviewTime', 'psSubconDiecutDate', 'psSubconDiecutTime', 'psDielineDevelopmentDate', 'psDielieneDevelopmentTime', 'psDielineDevelopmentTime', 'psImageDeliveryProductionDate', 'psImageDeliveryProductionTime', 'psDiecutDeliveryProductionDate', 'psDiecutDeliveryProductionTime', 'psDielineDeliveryProductionDate', 'psDielineDeliveryProductionTime', 'isCanceled', 'isRead', 'codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'createdProductionDateTime', 'modifiedProductionDateTime', 'transactionDate', 'note'],
                'field_operators_list' => [
                    'developmentType' => [SortAscending::class, SortDescending::class],
                    'isCanceled' => [SortAscending::class, SortDescending::class],
                    'codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'transactionDate' => [SortAscending::class, SortDescending::class],
                    'note' => [SortAscending::class, SortDescending::class],
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
