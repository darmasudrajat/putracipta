<?php

namespace App\Form\Accounting;

use App\Entity\Accounting\JournalVoucherHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JournalVoucherHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isCanceled')
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JournalVoucherHeader::class,
        ]);
    }
}
