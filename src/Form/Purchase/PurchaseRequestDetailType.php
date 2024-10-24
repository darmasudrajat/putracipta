<?php

namespace App\Form\Purchase;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Master\Material;
use App\Entity\Purchase\PurchaseRequestDetail;
use App\Entity\Stock\InventoryRequestMaterialDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseRequestDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', FormattedNumberType::class, ['decimals' => 0])
            ->add('unit', null, [
                'choice_label' => 'name', 
                'label' => 'Satuan',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false");
                },
            ])
            ->add('material', EntityHiddenType::class, ['class' => Material::class])
            ->add('inventoryRequestMaterialDetail', EntityHiddenType::class, ['class' => InventoryRequestMaterialDetail::class])
            ->add('usageDate', null, ['widget' => 'single_text'])
            ->add('memo')
            ->add('isCanceled')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseRequestDetail::class,
        ]);
    }
}
