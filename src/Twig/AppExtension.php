<?php 

namespace App\Twig;

use App\App\UrlHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']),
            new TwigFilter('replaceIfNotFound', [$this, 'replaceFileIfNotFound']),
        ];
    }

    public function formatPrice($cents, $currency = 'EUR', $decimalSep = '.', $thousandsSep = ',', $decimals = 2): string
    {
        $price = $cents/100;
        $formatedPrice = number_format($price, $decimals, $decimalSep, $thousandsSep);
        switch($currency) {
            case "EUR":
                $withCurrency = $formatedPrice.'€';
                break;
            case "USD":
                $withCurrency = '$'.$formatedPrice;
            default:
                $withCurrency = $formatedPrice;
        }
        return $withCurrency;
    }

    public function replaceFileIfNotFound(string $fileUrl, string $fileType = 'image', ?string $defaultFile = null): ?string
    {
        if(UrlHelper::isReturningError($fileUrl)) {
            if(!$defaultFile) {
                switch($fileType) {
                    case 'image':
                        return '/img/image-placeholder.png';
                        break;
                    default:
                        return null;
                }
            }
            return $defaultFile;
        }
        return $fileUrl;
    }

}