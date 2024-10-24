<?php

namespace App\Form\Accounting;

use App\Entity\Accounting\AccountingLedger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountingLedgerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionType')
            ->add('transactionSubject')
            ->add('debitAmount')
            ->add('creditAmount')
            ->add('account')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AccountingLedger::class,
        ]);
    }
}
