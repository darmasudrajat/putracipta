<?php

namespace App\Common\Form\Type;

use App\Common\Form\DataTransformer\DateToFormattedTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormattedDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $transformer = new DateToFormattedTransformer($options['dateFormat']);
        $builder->addModelTransformer($transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(['dateFormat'])
        ;
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
