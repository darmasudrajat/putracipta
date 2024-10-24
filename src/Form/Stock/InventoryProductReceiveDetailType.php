<?php

namespace App\Form\Stock;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Master\Product;
use App\Entity\Production\MasterOrderProductDetail;
use App\Entity\Stock\InventoryProductReceiveDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryProductReceiveDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantityBox', FormattedNumberType::class, ['decimals' => 2])
            ->add('quantityBoxExtraPieces', FormattedNumberType::class, ['decimals' => 2])
            ->add('quantityPiecePerBox', FormattedNumberType::class, ['decimals' => 2])
            ->add('memo')
            ->add('isCanceled')
            ->add('product', EntityHiddenType::class, array('class' => Product::class))
            ->add('masterOrderProductDetail', EntityHiddenType::class, ['class' => MasterOrderProductDetail::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InventoryProductReceiveDetail::class,
        ]);
    }
}
