<?php

namespace App\Form\Purchase;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Master\Paper;
use App\Entity\Purchase\PurchaseRequestPaperDetail;
use App\Entity\Stock\InventoryRequestPaperDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseRequestPaperDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', FormattedNumberType::class, ['decimals' => 0])
            ->add('usageDate', null, ['widget' => 'single_text'])
            ->add('memo')
            ->add('isCanceled')
            ->add('paper', EntityHiddenType::class, array('class' => Paper::class))
            ->add('inventoryRequestPaperDetail', EntityHiddenType::class, array('class' => InventoryRequestPaperDetail::class))
            ->add('unit', null, [
                'choice_label' => 'name', 
                'label' => 'Satuan',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false");
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseRequestPaperDetail::class,
        ]);
    }
}
