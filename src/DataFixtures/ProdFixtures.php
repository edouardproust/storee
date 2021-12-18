<?php 

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;

/** 
 * Load fixtures for PROD environnement.
 * Use console command: "php bin/console doctrine: fixtures:load --env prod"
 */
class ProdFixtures extends AbstractFixture 
{

    // public function __construct()
    // {
    //     $this->faker = Factory::create();
    // }

    protected function getEnvironments()
    {
        return ['prod'];
    }

    protected function envLoad(ObjectManager $manager): void
    {
        // // create items
        // $this->createItems();
        
        // // persist
        // foreach($this->items as $item) {
        //     $manager->persist($item);
        // }

        // $manager->flush();
    }

    // private function createitems(): void
    // {
    //     $item = new Item();
    //     $item
    //         ->setName('test');
    //     $this->items[] = $item;
    // }
    
}