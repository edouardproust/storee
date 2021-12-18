<?php

namespace App\DataFixtures;

use App\Entity\AdminSetting;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

/** 
 * Auto-detect current environnement (dev / prod) and run fixtures accordingly.
 * To load, use command: "php bin/console doctrine:fixtures:load [--no-interaction]"
 */
class AppFixtures extends Fixture
{
    const ADMIN_SETTINGS = [
        'siteName' => 'Storee',
        'logo' => null,
        'colorPrimary' => '#2780e3',
        'colorPrimaryHover' => '#2780e3',
        'homeHeroImg' => '/img/home/hero-bg.jpg',
        'productPerCollectionPage' => 9,
        'directCheckout' => false,
        'contactEmail' => "contact@storee.io",
        'contactName' => 'Edouard at Storee.io',
    ];

    /** @var AdminSetting[] */
    private $adminSettings = [];

    public function load(ObjectManager $manager): void
    {
        // create entities
        $this->createAdminSettings();
        
        // save entities in an array
        if(!empty($this->adminSettings)) {
            foreach($this->adminSettings as $adminSetting) {
                $manager->persist($adminSetting);
            }
        }

        $manager->flush();
    }

    private function createAdminSettings(): void
    {
        foreach(self::ADMIN_SETTINGS as $slug => $value) {
            $setting = (new AdminSetting)
                ->setSlug($slug)
                ->setValue($value);
            $this->adminSettings[] = $setting;
        }
    }
}