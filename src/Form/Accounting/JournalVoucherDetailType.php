<?php

namespace App\Form\Accounting;

use App\Entity\Accounting\JournalVoucherDetail;
use App\Common\Form\Type\FormattedNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JournalVoucherDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('debitAmount', FormattedNumberType::class, ['decimals' => 2])
            ->add('creditAmount', FormattedNumberType::class, ['decimals' => 2])
            ->add('isCanceled')
            ->add('memo')
            ->add('account', null, [
                'choice_label' => 'name',
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
            'data_class' => JournalVoucherDetail::class,
        ]);
    }
}
