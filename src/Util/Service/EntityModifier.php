<?php

namespace App\Util\Service;

class EntityModifier
{
    public static function reset($object, array $exceptionFieldNames)
    {
        $reflectionClass = new \ReflectionClass(get_class($object));
        $newObject = $reflectionClass->newInstance();
        $reflectionProperties = $reflectionClass->getProperties();
        foreach ($reflectionProperties as $reflectionProperty) {
            if (!in_array($reflectionProperty->getName(), $exceptionFieldNames)) {
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($object, $reflectionProperty->getValue($newObject));
            }
        }
    }
}
