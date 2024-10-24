<?php

namespace App\Common\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterExpressionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $operators = $options['operators'] === null ? [] : $options['operators'];
        $operatorLabels = array_map(fn($operator) => (new $operator)->getLabel(), $operators);
        $choices = array_combine(array_values($operatorLabels), array_values($operators));
        $operatorOptions = $options['operator_options'] === null ? ['choices' => $choices, 'required' => false] : $options['operator_options'];
        $builder->add(0, ChoiceType::class, $operatorOptions);

        $maxValueCount = empty($operators) ? 0 : max(array_map(fn($operator) => (new $operator)->getValueCount(), $operators));
        for ($i = 1; $i <= $maxValueCount; $i++) {
            $valueType = $options['value_type'] === null ? TextType::class : $options['value_type'];
            $valueOptions = $options['value_options'] === null ? ['required' => false, 'empty_data' => ''] : $options['value_options'];
            $builder->add($i, $valueType, $valueOptions);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'operators' => null,
            'operator_options' => null,
            'value_type' => null,
            'value_options' => null,
            'value_options' => null,
            'label' => null,
        ]);
        $resolver->setAllowedTypes('operators', ['null', 'string[]']);
        $resolver->setAllowedTypes('operator_options', ['null', 'array']);
        $resolver->setAllowedTypes('value_type', ['null', 'string']);
        $resolver->setAllowedTypes('value_options', ['null', 'array']);
        $resolver->setAllowedTypes('label', ['null', 'string']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['field_label'] = $options['label'];
    }
}
