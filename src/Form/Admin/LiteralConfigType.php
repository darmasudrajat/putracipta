<?php

namespace App\Form\Admin;

use App\Entity\Admin\LiteralConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LiteralConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ifscCode', null, ['label' => 'FSC Code'])
            ->add('vatPercentage', null, ['label' => 'VAT'])
            ->add('serviceTaxPercentage', null, ['label' => 'PPh 23'])
            ->add('paymentRemainingTolerance', null, ['label' => 'Toleransi Sisa Pembayaran'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LiteralConfig::class,
        ]);
    }
}
