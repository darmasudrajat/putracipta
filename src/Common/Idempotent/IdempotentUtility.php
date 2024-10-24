<?php

namespace App\Common\Idempotent;

use Symfony\Component\HttpFoundation\Request;

class IdempotentUtility
{
    public static function generateTokenName(): string
    {
        return '_idempotent_token';
    }

    public static function generateTokenValue(): int
    {
        return random_int(1, 2000000000);
    }

    public static function check(Request $request): bool
    {
        return $request->request->has(self::generateTokenName()) && $request->attributes->has('_controller');
    }

    public static function create(string $idempotentClassName, Request $request): IdempotentInterface
    {
        $reflectionClass = new \ReflectionClass($idempotentClassName);
        $idempotentEntity = $reflectionClass->newInstance();

        $tokenNameSetter = 'set' . ucfirst($idempotentEntity->getTokenNameFieldName());
        $tokenValueSetter = 'set' . ucfirst($idempotentEntity->getTokenValueFieldName());
        $tokenDateSetter = 'set' . ucfirst($idempotentEntity->getTokenDateFieldName());

        $idempotentEntity->$tokenNameSetter($request->attributes->get('_controller'));
        $idempotentEntity->$tokenValueSetter($request->request->get(self::generateTokenName()));
        $idempotentEntity->$tokenDateSetter(new \DateTime());

        return $idempotentEntity;
    }
}
