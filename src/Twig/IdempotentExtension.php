<?php

namespace App\Twig;

use App\Common\Idempotent\IdempotentUtility;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IdempotentExtension extends AbstractExtension
{
    public function getName(): string
    {
        return 'idempotent_extension';
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('idempotent_token_value', [$this, 'functionIdempotentTokenValue']),
            new TwigFunction('idempotent_token_name', [$this, 'functionIdempotentTokenName']),
        ];
    }

    public function functionIdempotentTokenValue(): int
    {
        return IdempotentUtility::generateTokenValue();
    }

    public function functionIdempotentTokenName(): string
    {
        return IdempotentUtility::generateTokenName();
    }
}
