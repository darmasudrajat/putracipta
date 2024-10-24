<?php

namespace App\Form\Stock;

use App\Entity\Stock\Inventory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate')
            ->add('transactionType')
            ->add('transactionSubject')
            ->add('note')
            ->add('quantityIn')
            ->add('quantityOut')
            ->add('purchasePrice')
            ->add('product')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inventory::class,
        ]);
    }
}
