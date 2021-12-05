<?php

namespace App\App\Service;

use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;

class AccountService
{

    public function getUserData(Purchase $purchase)
    {
        return unserialize($purchase->getUserData());
    }

    public function getProductData(PurchaseItem $purchaseItem): Product
    {
        return unserialize($purchaseItem->getProductData());
    }

}