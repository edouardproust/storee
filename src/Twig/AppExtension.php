<?php 

namespace App\Twig;

use App\App\Path;
use App\Entity\User;
use Twig\TwigFilter;
use App\Entity\Upload;
use Twig\TwigFunction;
use App\Entity\Purchase;
use App\App\Entity\Modal;
use App\App\Entity\Collection;
use App\App\Helper\PriceHelper;
use App\App\Helper\TemplateHelper;
use App\App\Service\UploadService;
use Twig\Extension\AbstractExtension;
use App\App\Service\CollectionService;
use App\Repository\CategoryRepository;
use App\App\Service\AdminSettingService;
use App\Repository\PurchaseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;

class AppExtension extends AbstractExtension
{

    /** @var UploadService */
    private $uploadService;

    /** @var CategoryRepository */
    private $categoryRepository;

    /** @var AdminSettingService */
    private $adminSettingService;

    /** @var Path */
    private $path;

    /** @var CollectionService */
    private $collectionService;

    public function __construct(UploadService $uploadService, CategoryRepository $categoryRepository, Path $path, AdminSettingService $adminSettingService, CollectionService $collectionService, PurchaseRepository $purchaseRepository)
    {
        $this->uploadService = $uploadService;
        $this->categoryRepository = $categoryRepository;
        $this->path = $path;
        $this->adminSettingService = $adminSettingService;
        $this->collectionService = $collectionService;
        $this->purchaseRepository = $purchaseRepository;
    }    
    
    public function getFunctions()
    {
        return [
            new TwigFunction('setting', [$this, 'getSettingValue']),
            new TwigFunction('settingImage', [$this, 'getSettingImage']),
            new TwigFunction('categories', [$this, 'getAllCategories']),
            new TwigFunction('collectionFilterOptions', [$this, 'getCollectionFilterOptions']),
            new TwigFunction('collListThLink', [$this, 'getCollectionAdminListThLink']),
            new TwigFunction('modal', [$this, 'createModal']),
            new TwigFunction('userLastOrder', [$this, 'getUserLastOrder']),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']),
            new TwigFilter('fileExists', [$this, 'fileExists']),
            new TwigFilter('fileIcon', [$this, 'getFileTypeIcon']),
            new TwigFilter('uploadedImgUrl', [$this, 'getUploadedImageUrl']),
            new TwigFilter('strpos', [$this, 'strpos']),
            new TwigFilter('extract', [$this, 'extract']),
            new TwigFilter('popup', [$this, 'showPopup']),
            new TwigFilter('trigger', [$this, 'showModalTrigger'])
        ];
    }
    
    /**
     * Get value of an AdminSetting object, based on its slug
     * @param string $settingSlug The slug of the setting
     * @return null|string 
     */
    public function getSettingValue(string $slug): ?string
    {
        return $this->adminSettingService->getValue($slug);
    }

    /**
     * Get the url of an admin setting Upload object, based on its slug
     * @param string $settingSlug The slug of the setting
     * @return null|string The Upload object's 'url' field value
     */
    public function getSettingImage(string $slug): ?string
    {
        $upload = $this->adminSettingService->getUpload($slug);
        return $upload ? $this->adminSettingService->getUpload($slug)->getUrl() : null;
    }

    public function getAllCategories(): array
    {
        return $this->categoryRepository->findAll();
    }

    public function formatPrice($cents, $currency = 'EUR', $decimalSep = '.', $thousandsSep = ',', $decimals = 2): string
    {
        return PriceHelper::format($cents, $currency, $decimalSep, $thousandsSep, $decimals);
    }

    /** 
     * @return array [ value => label ]
     */
    public function getCollectionFilterOptions(): array
    {
        return $this->adminSettingService->getValue('collectionFilterOptions');
    }

    public function getCollectionAdminListThLink(string $label, string $orderBy, Request $request, Collection $collection): string
    {
        return $this->collectionService->getCollectionAdminListThLink($label, $orderBy, $request, $collection);
    }

    /**
     * Function to generate Modal object
     * @param null|int $entityId 
     * @return Modal 
     */
    public function createModal(string $modalId): Modal
    {
        return new Modal($modalId);
    }

    /**
     * Filter to display Modal window
     * @param Modal $modal 
     * @param null|string $title 
     * @param null|string $bodyHtml 
     * @return array 
     */
    public function showPopup(Modal $modal, ?string $title = null, ?string $bodyHtml = null, string $actionBtnLabel = null, ?string $actionBtnUrl = null): string
    {
        return $modal->showPopup($title, $bodyHtml, $actionBtnLabel, $actionBtnUrl);
    }

    /**
     * Filter to display Modal trigger.
     * @param Modal $modal  
     * @param string $btnLabel 
     * @param null|string $btnClass
     * @return array 
     */
    public function showModalTrigger(Modal $modal, string $btnLabel, ?string $btnClass = null): string
    {
        return $modal->showTrigger($btnLabel, $btnClass);
    }

    public function getUserLastOrder(User $user): ?Purchase
    {
        return $this->purchaseRepository->findLastOfUser($user);
    }

    /**
     * @param string|Upload $file 
     * @return bool 
     */
    public function fileExists($file)
    {
        return $this->uploadService->checkIfFileExists($file);
    }

    /**
     * Return file type in order to display icons for each format (with font-awesome icons) or a preview (if file is an image)
     * @param Upload $file 
     * @return bool 
     */
    public function getFileTypeIcon(Upload $file): ?string
    {
        if($file) {
            $fileObj = new File($this->path->ROOT() . $file->getUrl());
            $extension = $fileObj->guessExtension();
            switch($extension) {
                case 'jpg': return 'img'; break;
                case 'jpeg': return 'img'; break;
                case 'png': return 'img'; break;
                case 'webp': return 'img'; break;
                case 'gif': return 'img'; break;
                case 'txt': return 'file-alt'; break;
                case 'pdf': return 'file-pdf'; break;
                case 'csv': return 'file-csv'; break;
                case 'xls': return 'file-excel'; break;
                case 'xlsx': return 'file-excel'; break;
                case 'js': return 'file-code'; break;
                case 'css': return 'file-code'; break;
                case 'scss': return 'file-code'; break;
                case 'php': return 'file-code'; break;
                case 'html': return 'file-code'; break;
                case 'htm': return 'file-code'; break;
                case 'twig': return 'file-code'; break;
                case 'mpeg': return 'file-video'; break;
                case 'avi': return 'file-video'; break;
                case 'mp3': return 'file-audio'; break;
                default: return 'file'; break;
            }
        }
    }

    /**
     * @param Upload|string|null $upload Upload for a manually uploaded image. String for a setting or a fixture image. If null, method will return a default image placeholder.
     * @return null|string 
     */
    public function getUploadedImageUrl($upload)
    {
        return $this->uploadService->getUploadedImageUrl($upload);
    }

    /**
     * Tells if the string (haystack) contains another string (needle)
     * @param string $haystack Autofilled by twig 
     * @param string $needle The string to search for
     * @return bool 
     */
    public function strpos($haystack, string $needle): bool
    {
        if(strpos($haystack, $needle)) return true;
        return false;
    }

    /**
     * @param string $text Autofilled by twig
     * @param int int $maxChars How many characters long the extract should be?
     * @param string|null $delimiter The delimiter that ends the extract if the text is trimmed. Default: '...'
     * @return void 
     */
    public function extract($text, int $maxChars = 120, ?string $delimiter = '...')
    {
        return TemplateHelper::extract($text, $maxChars, $delimiter);
    }

}