<?php 

namespace App\App\Helper;

class PriceHelper
{

    /**
     * Transform a cents price int input (eg. 1990) into a formated price string output (eg. '19,90€')
     * 
     * @param int $cents Price in cents
     * @param string $currency 'EUR' or 'USD'. Default: 'EUR'
     * @param string $decimalSep Default: ',' (comma)
     * @param string $thousandsSep Default: ' ' (space)
     * @param int $decimals How many decimals? Default: 2
     * @return string 
     */
    public static function format(int $cents, $currency = 'EUR', $decimalSep = ',', $thousandsSep = ' ', $decimals = 2): string
    {
        $price = $cents/100;
        $formatedPrice = number_format($price, $decimals, $decimalSep, $thousandsSep);
        switch($currency) {
            case "EUR":
                $withCurrency = $formatedPrice.'€'; break;
            case "USD":
                $withCurrency = '$'.$formatedPrice; break;
            default:
                $withCurrency = $formatedPrice;
        }
        return $withCurrency;
    }

}