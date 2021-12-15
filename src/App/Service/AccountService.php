<?php

namespace App\App\Service;

use App\Entity\Product;
use App\Entity\PurchaseItem;
use App\Repository\ProductRepository;
use stdClass;

class AccountService
{

    public function getProductData(PurchaseItem $purchaseItem): Product
    {
        return unserialize($purchaseItem->getProductData());
    }

}