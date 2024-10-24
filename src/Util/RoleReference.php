<?php

namespace App\Util;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class RoleReference
{
    private array $securityRoles;

    public function __construct(ContainerBagInterface $containerBag)
    {
        $this->securityRoles = $containerBag->get('security.role_hierarchy.roles');
    }

    public function getData(): array
    {
        $rolesByParent = [];
        $rolesByParents = [];
        foreach ($this->securityRoles as $role => $roleChildren) {
            if (!isset($rolesByParent[$role])) {
                $rolesByParent[$role] = null;
            }
            foreach ($roleChildren as $roleChild) {
                if (!isset($rolesByParent[$roleChild])) {
                    $rolesByParent[$roleChild] = $role;
                }
            }
        }
        foreach ($rolesByParent as $role => $roleParent) {
            $rolesByParents[$role] = $this->getParentRoles($rolesByParent, $role);
        }
        return $rolesByParents;
    }

    private function getParentRoles(array $roles, ?string $name): array
    {
        $parentRoles = [];
        $currentName = $name;
        while (!empty($roles[$currentName])) {
            $parentRoles[] = $roles[$currentName];
            $currentName = $roles[$currentName];
        }
        return $parentRoles;
    }
}
