<?php

namespace App\Twig;

use App\Util\NumberWord;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FormatExtension extends AbstractExtension
{
    public function getName(): string
    {
        return 'format_extension';
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('say', [$this, 'filterSay']),
        ];
    }

    public function filterSay(string $number): string
    {
        return NumberWord::name($number);
    }
}
