<?php

namespace App\Form\Purchase;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Master\Paper;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use App\Entity\Purchase\PurchaseRequestPaperDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseOrderPaperDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', FormattedNumberType::class, ['decimals' => 0])
            ->add('apkiValue')
            ->add('associationPrice', FormattedNumberType::class, ['decimals' => 2])
            ->add('weightPrice', FormattedNumberType::class, ['decimals' => 2])
            ->add('unitPrice', FormattedNumberType::class, ['decimals' => 2])
            ->add('deliveryDate', null, ['widget' => 'single_text'])
            ->add('paper', EntityHiddenType::class, array('class' => Paper::class))
            ->add('purchaseRequestPaperDetail', EntityHiddenType::class, array('class' => PurchaseRequestPaperDetail::class))
            ->add('unit', null, [
                'choice_label' => 'name', 
                'label' => 'Satuan',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false");
                },
            ])
            ->add('isTransactionClosed')
            ->add('isCanceled')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseOrderPaperDetail::class,
        ]);
    }
}
