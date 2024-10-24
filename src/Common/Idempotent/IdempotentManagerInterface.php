<?php

namespace App\Common\Idempotent;

interface IdempotentManagerInterface
{
    public function checkAndAdd(IdempotentInterface $idempotentEntity): void;

    public function getTokenName(): string;

    public function getMinTokenValue(): int;

    public function getMaxTokenValue(): int;
}
