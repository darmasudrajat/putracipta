<?php

namespace App\Util\Service;

use App\Entity\Stock\Inventory;

class InventoryUtil
{
    public static function reverseOldData($repository, $formDataEntity): void
    {
        $lastInventoryItems = $repository->findBy([
            'transactionCodeNumberOrdinal' => $formDataEntity->getCodeNumberOrdinal(),
            'transactionCodeNumberMonth' => $formDataEntity->getCodeNumberMonth(),
            'transactionCodeNumberYear' => $formDataEntity->getCodeNumberYear(),
            'transactionType' => $formDataEntity->getCodeNumberConstant(),
            'isReversed' => false,
        ]);
        foreach ($lastInventoryItems as $lastInventoryItem) {
            $lastInventoryItem->setIsReversed(true);
            $repository->add($lastInventoryItem);
            $reversedInventory = new Inventory();
            $reversedInventory->setTransactionCodeNumberOrdinal($lastInventoryItem->getTransactionCodeNumberOrdinal());
            $reversedInventory->setTransactionCodeNumberMonth($lastInventoryItem->getTransactionCodeNumberMonth());
            $reversedInventory->setTransactionCodeNumberYear($lastInventoryItem->getTransactionCodeNumberYear());
            $reversedInventory->setTransactionDate($lastInventoryItem->getTransactionDate());
            $reversedInventory->setTransactionType($lastInventoryItem->getTransactionType());
            $reversedInventory->setTransactionSubject($lastInventoryItem->getTransactionSubject());
            $reversedInventory->setMaterial($lastInventoryItem->getMaterial());
            $reversedInventory->setPaper($lastInventoryItem->getPaper());
            $reversedInventory->setProduct($lastInventoryItem->getProduct());
            $reversedInventory->setWarehouse($lastInventoryItem->getWarehouse());
            $reversedInventory->setInventoryMode($lastInventoryItem->getInventoryMode());
            $reversedInventory->setCreatedInventoryDateTime($lastInventoryItem->getCreatedInventoryDateTime());
            $reversedInventory->setNote($lastInventoryItem->getNote());
            $reversedInventory->setPurchasePrice(-($lastInventoryItem->getPurchasePrice()));
            $reversedInventory->setQuantityIn(-($lastInventoryItem->getQuantityIn()));
            $reversedInventory->setQuantityOut(-($lastInventoryItem->getQuantityOut()));
            $reversedInventory->setIsReversed(true);
            $repository->add($reversedInventory);
        }
    }

    public static function addNewData($inventoryRepository, $formDataEntity, array $formDataEntityDetails, callable $setDataFunction): void
    {
        foreach ($formDataEntityDetails as $formDataEntityDetail) {
            if (!$formDataEntityDetail->isIsCanceled()) {
                $newInventory = new Inventory();
                $newInventory->setTransactionCodeNumberOrdinal($formDataEntity->getCodeNumberOrdinal());
                $newInventory->setTransactionCodeNumberMonth($formDataEntity->getCodeNumberMonth());
                $newInventory->setTransactionCodeNumberYear($formDataEntity->getCodeNumberYear());
                $newInventory->setTransactionDate($formDataEntity->getTransactionDate());
                $newInventory->setTransactionType($formDataEntity->getCodeNumberConstant());
                $newInventory->setNote($formDataEntity->getNote());
                $newInventory->setCreatedInventoryDateTime(new \DateTime());
                $setDataFunction($newInventory, $formDataEntityDetail);
                $inventoryRepository->add($newInventory);
            }
        }
    }

    public static function getAveragePriceList($itemFieldName, $transactionRepository, $formDataEntityDetails): array
    {
        $getterName = 'get' . ucfirst($itemFieldName);
        $items = array_map(fn($formDataEntityDetail) => $formDataEntityDetail->$getterName(), $formDataEntityDetails);
        $averagePriceList = $transactionRepository->getAveragePriceList($items);
        $averagePriceListIndexed = array_column($averagePriceList, 'averagePrice', "{$getterName}Id");
        return $averagePriceListIndexed;
    }
}
