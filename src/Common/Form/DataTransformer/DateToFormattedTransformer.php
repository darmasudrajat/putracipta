<?php

namespace App\Common\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class DateToFormattedTransformer implements DataTransformerInterface
{
    private string $dateFormat;

    public function __construct(string $dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    public function transform($date)
    {
        return $date === null ? '' : $date->format($this->dateFormat);
    }

    public function reverseTransform($formatted)
    {
        return $formatted === '' ? null : date_create_from_format($this->dateFormat, $formatted);
    }
}
