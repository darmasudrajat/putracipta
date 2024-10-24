<?php

namespace App\Form\Stock;

use App\Common\Form\Type\FormattedDateType;
use App\Entity\Stock\AdjustmentStockMaterialDetail;
use App\Entity\Stock\AdjustmentStockPaperDetail;
use App\Entity\Stock\AdjustmentStockProductDetail;
use App\Entity\Stock\AdjustmentStockHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdjustmentStockHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $adjustmentModeChoices = $options['isFinishedGoods'] ? [
            'Finished Goods' => AdjustmentStockHeader::ADJUSTMENT_MODE_PRODUCT,
        ] : [
            'Material' => AdjustmentStockHeader::ADJUSTMENT_MODE_MATERIAL,
            'Kertas' => AdjustmentStockHeader::ADJUSTMENT_MODE_PAPER,
        ];
        $builder
            ->add('transactionDate', FormattedDateType::class)
            ->add('note')
            ->add('adjustmentMode', ChoiceType::class, ['multiple' => false, 'expanded' => false, 'placeholder' => '-- Select Mode --', 'choices' => $adjustmentModeChoices])
            ->add('warehouse', null, [
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false")
                            ->addOrderBy('e.name', 'ASC');
                },
            ]);
        if ($options['isFinishedGoods']) {
            $builder->add('adjustmentStockProductDetails', CollectionType::class, [
                'entry_type' => AdjustmentStockProductDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new AdjustmentStockProductDetail(),
                'label' => false,
            ]);
        } else {
            $builder
                ->add('adjustmentStockMaterialDetails', CollectionType::class, [
                    'entry_type' => AdjustmentStockMaterialDetailType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'prototype_data' => new AdjustmentStockMaterialDetail(),
                    'label' => false,
                ])
                ->add('adjustmentStockPaperDetails', CollectionType::class, [
                    'entry_type' => AdjustmentStockPaperDetailType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'prototype_data' => new AdjustmentStockPaperDetail(),
                    'label' => false,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['isFinishedGoods']);
        $resolver->setDefaults([
            'data_class' => AdjustmentStockHeader::class,
        ]);
    }
}
