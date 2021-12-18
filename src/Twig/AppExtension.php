<?php 

namespace App\Twig;

use Twig\TwigFilter;
use Twig\TwigFunction;
use App\App\Helper\UrlHelper;
use Twig\Extension\AbstractExtension;
use App\Repository\CategoryRepository;
use App\Repository\AdminSettingRepository;
use InvalidArgumentException;
use RuntimeException;
use Doctrine\ORM\NonUniqueResultException;

class AppExtension extends AbstractExtension
{

    /** @var AdminSettingRepository */
    private $settings;

    /** @var CategoryRepository */
    private $categoryRepository;

    public function __construct(AdminSettingRepository $adminSettingRepository, CategoryRepository $categoryRepository)
    {
        $this->settings = $adminSettingRepository;
        $this->categoryRepository = $categoryRepository;
    }    
    
    public function getFunctions()
    {
        return [
            new TwigFunction('setting', [$this, 'getSetting']),
            new TwigFunction('categories', [$this, 'getAllCategories'])
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']),
            new TwigFilter('replaceIfNotFound', [$this, 'replaceFileIfNotFound']),
        ];
    }

    /**
     * Show value of an AdminSetting object, based on its slug
     * @param string $settingSlug The slug of the setting
     * @return null|string 
     */
    public function getSetting(string $slug): ?string
    {
        return $this->settings->get($slug);
    }

    public function getAllCategories(): array
    {
        return $this->categoryRepository->findAll();
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