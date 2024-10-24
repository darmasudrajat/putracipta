<?php

namespace App\Util;

use App\Config\UserMenuConfig;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class UserMenu
{
    private Security $security;
    private RequestStack $requestStack;
    private array $menuHierarchy;
    private array $menuAttributes;
    private array $reversedMenuHierarchy;

    public function __construct(Security $security, RequestStack $requestStack)
    {
        $this->security = $security;
        $this->requestStack = $requestStack;
        $this->menuHierarchy = UserMenuConfig::getMenuHierarchy();
        $this->menuAttributes = UserMenuConfig::getMenuAttributes();
        $this->reversedMenuHierarchy = $this->reverseMenuHierarchy();
    }

    public function getMenuHierarchy(): array
    {
        return $this->menuHierarchy;
    }

    public function getMenuAttributes(): array
    {
        return $this->menuAttributes;
    }

    public function getReversedMenuHierarchy(): array
    {
        return $this->reversedMenuHierarchy;
    }

    public function isAuthorized(string $name): bool
    {
        if (!empty($this->menuHierarchy[$name])) {
            $valid = false;
            foreach ($this->menuHierarchy[$name] as $itemName) {
                $valid = $valid || $this->isAuthorized($itemName);
                if ($valid) {
                    break;
                }
            }
            return $valid;
        } else {
            $roles = $this->menuAttributes[$name]['roles'];
            $roleValids = array_map(fn($role) => $this->security->isGranted($role), $roles);
            $valid = array_reduce($roleValids, fn($a, $b) => $a || $b, false);
            return $valid;
        }
    }

    public function isAccessed(string $name): bool
    {
        if (!empty($this->menuHierarchy[$name])) {
            $valid = false;
            foreach ($this->menuHierarchy[$name] as $itemName) {
                $valid = $valid || $this->isAccessed($itemName);
                if ($valid) {
                    break;
                }
            }
            return $valid;
        } else {
            $route = $this->menuAttributes[$name]['route'];
            $pattern = isset($this->menuAttributes[$name]['pattern']) ? $this->menuAttributes[$name]['pattern'] : null;
            $currentRoute = $this->requestStack->getCurrentRequest()->get('_route');
            $valid = false;
            if ($pattern === null) {
                $valid = $currentRoute === $route;
            } else if (is_string($pattern)) {
                $valid = preg_match($pattern, $currentRoute) === 1;
            } else {
                $valid = false;
            }
            return $valid;
        }
    }

    public function getNumberOfParents(string $name): int
    {
        $num = 0;
        $searchName = $name;
        while (isset($this->reversedMenuHierarchy[$searchName])) {
            $searchName = $this->reversedMenuHierarchy[$searchName];
            $num++;
        }
        return $num;
    }

    private function reverseMenuHierarchy(): array
    {
        $arr = [];
        foreach ($this->menuHierarchy as $name => $subNames) {
            $arr[$name] = null;
            foreach ($subNames as $subName) {
                $arr[$subName] = $name;
            }
        }
        return $arr;
    }
}