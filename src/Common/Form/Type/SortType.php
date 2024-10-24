<?php

namespace App\Common\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $operatorsList = $options['field_operators_list'];
        $operatorOptionsList = $options['field_operator_options_list'];
        foreach ($options['field_names'] as $fieldName) {
            $operators = !isset($operatorsList[$fieldName]) || $operatorsList[$fieldName] === null ? [] : $operatorsList[$fieldName];
            $operatorLabels = array_map(fn($operator) => (new $operator)->getLabel(), $operators);
            $choices = array_combine(array_values($operatorLabels), array_values($operators));
            $operatorOptions = !isset($operatorOptionsList[$fieldName]) || $operatorOptionsList[$fieldName] === null ? ['choices' => $choices, 'required' => false] : $operatorOptionsList[$fieldName];
            $builder->add($fieldName, ChoiceType::class, $operatorOptions);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'field_names' => [],
            'field_operators_list' => [],
            'field_operator_options_list' => [],
            'field_label_list' => [],
        ]);
        $resolver->setAllowedTypes('field_names', 'string[]');
        $resolver->setAllowedTypes('field_operators_list', 'array');
        $resolver->setAllowedTypes('field_operator_options_list', 'array');
        $resolver->setAllowedTypes('field_label_list', 'array');
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['field_label_list'] = $options['field_label_list'];
    }
}
