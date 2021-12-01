<?php

namespace App\App\Account;

use App\Entity\Purchase;
use stdClass;

class AccountHelper
{

    public function getUserData(Purchase $purchase): stdClass
    {
        return json_decode($purchase->getUserData());
    }

    public function getPaymentStatusConst()
    {
        $stdClass = new stdClass;
        $stdClass->pending = Purchase::STATUS_PENDING;
        $stdClass->paid = Purchase::STATUS_PAID;
        return $stdClass;
    }

}