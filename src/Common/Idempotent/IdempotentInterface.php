<?php

namespace App\Common\Idempotent;

interface IdempotentInterface
{
    public function getTokenNameFieldName(): string;

    public function getTokenValueFieldName(): string;

    public function getTokenDateFieldName(): string;
}
