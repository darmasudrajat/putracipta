<?php

namespace App\Form\Production;

use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Production\ProductPrototypePilotDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPrototypePilotDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productName')
            ->add('size')
            ->add('quantity', FormattedNumberType::class, ['decimals' => 0])
            ->add('memo')
            ->add('isCanceled')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductPrototypePilotDetail::class,
        ]);
    }
}
