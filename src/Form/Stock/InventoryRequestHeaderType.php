<?php

namespace App\Form\Stock;

use App\Common\Form\Type\FormattedDateType;
use App\Entity\Stock\InventoryRequestMaterialDetail;
use App\Entity\Stock\InventoryRequestPaperDetail;
use App\Entity\Stock\InventoryRequestHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryRequestHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('partNumber')
            ->add('warehouse', null, [
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false")
                            ->addOrderBy('e.name', 'ASC');
                },
            ])
            ->add('division', null, [
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false")
                            ->addOrderBy('e.name', 'ASC');
                },
            ])
            ->add('pickupDate', FormattedDateType::class)
            ->add('transactionDate', FormattedDateType::class)
            ->add('note')
            ->add('requestMode', ChoiceType::class, ['multiple' => false, 'expanded' => false, 'choices' => [
                'Material' => InventoryRequestHeader::REQUEST_MODE_MATERIAL,
                'Kertas' => InventoryRequestHeader::REQUEST_MODE_PAPER,
            ]])
            ->add('inventoryRequestMaterialDetails', CollectionType::class, [
                'entry_type' => InventoryRequestMaterialDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new InventoryRequestMaterialDetail(),
                'label' => false,
            ])
            ->add('inventoryRequestPaperDetails', CollectionType::class, [
                'entry_type' => InventoryRequestPaperDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new InventoryRequestPaperDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InventoryRequestHeader::class,
        ]);
    }
}
