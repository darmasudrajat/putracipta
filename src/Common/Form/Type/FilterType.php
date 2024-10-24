<?php

namespace App\Common\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $operatorsList = $options['field_operators_list'];
        $operatorOptionsList = $options['field_operator_options_list'];
        $valueTypeList = $options['field_value_type_list'];
        $valueOptionsList = $options['field_value_options_list'];
        $labelList = $options['field_label_list'];
        foreach ($options['field_names'] as $fieldName) {
            $builder->add($fieldName, FilterExpressionType::class, [
                'operators' => isset($operatorsList[$fieldName]) ? $operatorsList[$fieldName] : null,
                'operator_options' => isset($operatorOptionsList[$fieldName]) ? $operatorOptionsList[$fieldName] : null,
                'value_type' => isset($valueTypeList[$fieldName]) ? $valueTypeList[$fieldName] : null,
                'value_options' => isset($valueOptionsList[$fieldName]) ? $valueOptionsList[$fieldName] : null,
                'label' => isset($labelList[$fieldName]) ? $labelList[$fieldName] : null,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'field_names' => [],
            'field_operators_list' => [],
            'field_operator_options_list' => [],
            'field_value_type_list' => [],
            'field_value_options_list' => [],
            'field_label_list' => [],
        ]);
        $resolver->setAllowedTypes('field_names', 'string[]');
        $resolver->setAllowedTypes('field_operators_list', 'array');
        $resolver->setAllowedTypes('field_operator_options_list', 'array');
        $resolver->setAllowedTypes('field_value_type_list', 'array');
        $resolver->setAllowedTypes('field_value_options_list', 'array');
        $resolver->setAllowedTypes('field_label_list', 'array');
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $operatorValueCounts = [];
        foreach ($options['field_operators_list'] as $operators) {
            foreach ($operators as $operator) {
                $operatorValueCounts[$operator] = (new $operator)->getValueCount();
            }
        }
        $view->vars['operator_value_counts'] = $operatorValueCounts;
    }
}
