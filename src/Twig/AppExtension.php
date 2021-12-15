<?php 

namespace App\Twig;

use App\App\Helper\UrlHelper;
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

    /**
     * Warning: slows down the site a lot if used several times on the same page (ie. for a product collection)
     * @param string $fileUrl 
     * @param string $fileType 
     * @param null|string $defaultFile 
     * @return null|string 
     */
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