<?php

namespace App\DataFixtures;

use App\Config;
use App\App\Path;
use App\Entity\User;
use App\Entity\Upload;
use App\Entity\AdminSetting;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/** 
 * Auto-detect current environnement (dev / prod) and run fixtures accordingly.
 * To load, use command: "php bin/console doctrine:fixtures:load [--no-interaction]"
 */
class AppFixtures extends Fixture
{

    const ADMIN_USERNAME = Config::ADMIN_USERNAME;
    const ADMIN_PASSWORD = Config::ADMIN_PASSWORD;
    const ADMIN_COUNTRY = Config::ADMIN_COUNTRY;

    const ADMIN_SETTINGS_UPLOADS = [
        'logo' =>  'logo-color.png',
        'favicon' =>  'favicon.png',
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

    /** @var string */
    private $admin;

    /** @var AdminSetting[] */
    private $adminSettings = [];

    /** @var Upload[] */
    private $adminSettingImages = [];

    /** @var Path */
    private $path;

    public function __construct(Path $path, UserPasswordHasherInterface $hasher)
    {
        $this->path = $path;
        $this->hasher = $hasher;
        $this->faker = \Faker\Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        // create entities
        $this->createAdmin();
        $this->createAdminSettingImages();
        $this->createAdminSettings();

        // persist
        $manager->persist($this->admin);
        foreach ($this->adminSettingImages as $upload) {
            $manager->persist($upload);
        }
        foreach ($this->adminSettings as $adminSetting) {
            $manager->persist($adminSetting);
        }

        // flush
        $manager->flush();
    }

    private function createAdmin(): void
    {
        $admin = new User();
        $admin
            ->setEmail(self::ADMIN_USERNAME)
            ->setFirstname('Sygno')
            ->setLastname('Studio')
            ->setPassword($this->hasher->hashPassword($admin, self::ADMIN_PASSWORD))
            ->setRoles(['ROLE_ADMIN'])
            ->setCreatedAt(new \DateTime('yesterday'))
            ->setStreet($this->faker->streetAddress())
            ->setPostcode($this->faker->postcode())
            ->setCity($this->faker->city())
            ->setCountry(self::ADMIN_COUNTRY)
            ->setPhone($this->faker->phoneNumber());
        $this->admin = $admin;
    }

    private function createAdminSettingImages(): void
    {
        foreach (self::ADMIN_SETTINGS_UPLOADS as $slug => $fileName) {
            $this->adminSettingImages[$slug] = (new Upload)
                ->setName('fixture-setting-' . $slug)
                ->setUrl($this->path->IMG_SETTINGS_DEFAULT_REL() . $fileName);
        }
    }

    private function createAdminSettings(): void
    {
        foreach ($this->adminSettingImages as $slug => $upload) {
            $setting = (new AdminSetting())
                ->setSlug($slug)
                ->setUpload($upload);
            $this->adminSettings[] = $setting;
        }
        foreach (self::ADMIN_SETTINGS_VALUES as $slug => $value) {
            $setting = (new AdminSetting())
                ->setSlug($slug)
                ->setValue($value);
            $this->adminSettings[] = $setting;
        }
    }
}
