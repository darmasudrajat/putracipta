<?php

namespace App\Common\Form\Type;

use App\Common\Data\Criteria\DataCriteriaPagination;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaginationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['size_choices'] === null) {
            $builder->add('size', IntegerType::class, ['required' => false, 'empty_data' => '10']);
        } else {
            $builder->add('size', ChoiceType::class, [
                'choices' => $options['size_choices'],
                'choice_label' => fn($choice, $key, $value) => $value,
            ]);
        }
        $builder->add('number', IntegerType::class, ['required' => false, 'empty_data' => '1']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DataCriteriaPagination::class,
            'size_choices' => null,
        ]);
        $resolver->setAllowedTypes('size_choices', ['null', 'int[]']);
    }
}
