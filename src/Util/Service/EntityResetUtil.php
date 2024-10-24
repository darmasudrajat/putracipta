<?php

namespace App\Util\Service;

class EntityResetUtil
{
    public static function reset($formSync, $rootEntity): void
    {
        if ($rootEntity->isIsCanceled()) {
            $formSync->doAfterWhileScanning($rootEntity, function($entity) {
                $entity->setIsCanceled(true);
//                EntityModifier::reset($entity, ['id', 'isCanceled']);
            });
        }
    }
}
