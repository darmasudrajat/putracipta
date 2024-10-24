<?php

namespace App\Form\Stock;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Master\Paper;
use App\Entity\Stock\InventoryReleasePaperDetail;
use App\Entity\Stock\InventoryRequestPaperDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryReleasePaperDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', FormattedNumberType::class, ['decimals' => 2])
            ->add('memo')
            ->add('isCanceled')
            ->add('unit', null, [
                'choice_label' => 'name', 
                'label' => 'Satuan',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false");
                },
            ])
            ->add('paper', EntityHiddenType::class, array('class' => Paper::class))
            ->add('inventoryRequestPaperDetail', EntityHiddenType::class, ['class' => InventoryRequestPaperDetail::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InventoryReleasePaperDetail::class,
        ]);
    }
}
