<?php

namespace App\Form\Accounting;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Accounting\ExpenseDetail;
use App\Entity\Master\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpenseDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('amount', FormattedNumberType::class, ['decimals' => 2])
            ->add('isCanceled')
            ->add('memo')
            ->add('account', EntityHiddenType::class, ['class' => Account::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExpenseDetail::class,
        ]);
    }
}
