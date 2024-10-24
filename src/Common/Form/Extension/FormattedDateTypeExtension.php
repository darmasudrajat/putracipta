<?php

namespace App\Common\Form\Extension;

use App\Common\Form\Type\FormattedDateType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormattedDateTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [FormattedDateType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'dateFormat' => 'j M Y',
        ]);
    }
}
