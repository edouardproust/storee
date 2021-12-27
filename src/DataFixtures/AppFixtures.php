<?php

namespace App\DataFixtures;

use App\App\Path;
use App\Entity\Upload;
use App\Entity\AdminSetting;
use App\App\Service\UploadService;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/** 
 * Auto-detect current environnement (dev / prod) and run fixtures accordingly.
 * To load, use command: "php bin/console doctrine:fixtures:load [--no-interaction]"
 */
class AppFixtures extends Fixture
{

    const ADMIN_SETTINGS_UPLOADS = [
        'logo' =>  'logo-color.png',
        'homeHero' => 'home-hero-bg.jpg',
    ];

    const ADMIN_SETTINGS_VALUES = [
        // general
        'siteName' => 'Storee',
        'storeEmail' => 'contact@storee.io',
        'storeEmailExpeditor' => 'Edouard at Storee.io',
        'colorMain' => '#2780e3',
        'colorMainHover' => '#2780e3',
        // home sections
        'homeHeroPosition' => 'top',
        'homeHeroLayerOpacity' => 50,
        'entitiesPerAdminListPage' => 20,
        'homeCollectionItemsNumber' => 3,
        'homePopularProductsCriteria' => 'purchases',
        // store
        'collectionItemsPerPage' => 9,
        'collectionItemsPerRow' => 3,
        'collectionFilterOptions' => [
            'purchases' => 'Best sellers',
            'createdAt' => 'Newly added',
            'views' => 'Most visited'
        ],
        'collectionFilterDefault' => 'purchases',
        'directCheckout' => 0,
        // admin panel
        'entitiesPerAdminListPage' => 20
    ];

    /** @var AdminSetting[] */
    private $adminSettings = [];

    /** @var Upload[] */
    private $adminSettingImages = [];

    /** @var Path */
    private $path;

    public function __construct(Path $path)
    {
        $this->path = $path;
    }

    public function load(ObjectManager $manager): void
    {
        // create entities
        $this->createAdminSettingImages();
        $this->createAdminSettings();
        
        // persist
        foreach($this->adminSettingImages as $upload) {
            $manager->persist($upload);
        }
        foreach($this->adminSettings as $adminSetting) {
            $manager->persist($adminSetting);
        }

        // flush
        $manager->flush();
    }

    private function createAdminSettingImages(): void
    {
        foreach(self::ADMIN_SETTINGS_UPLOADS as $slug => $fileName) {
            $this->adminSettingImages[$slug] = (new Upload)
                ->setName('fixture-setting-'.$slug)
                ->setUrl($this->path->UPLOADS_SETTINGS_REL().$fileName);
        }
    }

    private function createAdminSettings(): void
    {
        foreach($this->adminSettingImages as $slug => $upload) {
            $setting = (new AdminSetting())
                ->setSlug($slug)
                ->setUpload($upload);
            $this->adminSettings[] = $setting;
        }
        foreach(self::ADMIN_SETTINGS_VALUES as $slug => $value) {
            $setting = (new AdminSetting())
                ->setSlug($slug)
                ->setValue($value);
            $this->adminSettings[] = $setting;
        }
    }
    
}