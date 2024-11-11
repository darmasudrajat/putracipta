<?php

namespace App\Form;

use App\Entity\Hasilpond;


use App\Entity\Master\Supplier;
use App\Common\Form\Type\EntityHiddenType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// use App\Entity\Purchase\PurchaseOrderDetail;
// use App\Entity\Purchase\PurchaseOrderHeader;

class HasilpondType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('nomo')
            ->add('supplier', EntityHiddenType::class, ['class' => Supplier::class])

            ->add('operator')
            ->add('good')
            ->add('ng')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Hasilpond::class,
        ]);
    }
}
