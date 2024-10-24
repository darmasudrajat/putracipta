<?php

namespace App\Common\Form\Extension;

use App\Common\Form\Type\FormattedNumberType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormattedNumberTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [FormattedNumberType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'decimals' => 0,
            'decimal_separator' => ',',
            'thousands_separator' => '.',
        ]);
    }
}
