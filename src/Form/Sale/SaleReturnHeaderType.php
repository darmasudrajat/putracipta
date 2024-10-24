<?php

namespace App\Form\Sale;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedDateType;
use App\Entity\Sale\DeliveryHeader;
use App\Entity\Sale\SaleReturnDetail;
use App\Entity\Sale\SaleReturnHeader;
use App\Repository\Admin\LiteralConfigRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleReturnHeaderType extends AbstractType
{
    private LiteralConfigRepository $literalConfigRepository;

    public function __construct(LiteralConfigRepository $literalConfigRepository)
    {
        $this->literalConfigRepository = $literalConfigRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $vatPercentage = $this->literalConfigRepository->findLiteralValue('vatPercentage');
        $builder
            ->add('transactionDate', FormattedDateType::class)
            ->add('referenceNumber')
            ->add('taxNumber')
            ->add('note')
            ->add('isProductExchange')
            ->add('warehouse', null, [
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false")
                            ->addOrderBy('e.name', 'ASC');
                },
            ])
//            ->add('taxMode', ChoiceType::class, ['choices' => [
//                '0%' => SaleReturnHeader::TAX_MODE_NON_TAX,
//                "{$vatPercentage}%" => SaleReturnHeader::TAX_MODE_TAX_EXCLUSION,
//            ]])
            ->add('deliveryHeader', EntityHiddenType::class, ['class' => DeliveryHeader::class])
            ->add('saleReturnDetails', CollectionType::class, [
                'entry_type' => SaleReturnDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new SaleReturnDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SaleReturnHeader::class,
        ]);
    }
}
