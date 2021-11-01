<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture {


    const MAX_CAT_NB = 8;
    const MAX_PRODUCTS_NB = 100;

    public function load(ObjectManager $manager): void
    {

        $slugger = new AsciiSlugger;
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        // categories

        $catUndefined = new Category;
        $catUndefined->setName("Undefined")
            ->setSlug("undefined");
        $manager->persist($catUndefined);

        $categoryIDs = [];
        $categoryNames = [];
        for($c = 1; $c <= self::MAX_CAT_NB; $c++) {
            $cat = new Category;
            // check that each cat name is unique
            $fakeCatName = $faker->category();
            while(in_array($fakeCatName, $categoryNames)) {
                $fakeCatName = $faker->category();
            }
            $categoryNames[] = $fakeCatName;
            // register category
            $cat
                ->setName($fakeCatName)
                ->setSlug(strtolower($slugger->slug($cat->getName())));
            $manager->persist($cat);
            $categoryIDs[] = $cat;
        }

        // products

        for($p = 1; $p <= self::MAX_PRODUCTS_NB; $p++) {
            $product = new Product;
            $product
                ->setName($faker->productName())
                ->setPrice($faker->price())
                ->setSlug(strtolower($slugger->slug($product->getName())))
                ->setShortDescription($faker->paragraph())
                ->setMainImage($faker->imageUrl(600, 450, true))
                ->setCreatedAt($faker->dateTimeBetween('-1 year', 'now'))
                ->setCategory($faker->randomElement($categoryIDs));
            $manager->persist($product);
        }

        // flush 

        $manager->flush();
    }
}