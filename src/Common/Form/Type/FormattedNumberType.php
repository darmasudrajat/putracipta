<?php

namespace App\Common\Form\Type;

use App\Common\Form\DataTransformer\NumberToFormattedTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormattedNumberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $transformer = new NumberToFormattedTransformer($options['decimals'], $options['decimal_separator'], $options['thousands_separator']);
        $builder->addModelTransformer($transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(['decimals', 'decimal_separator', 'thousands_separator'])
        ;
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
