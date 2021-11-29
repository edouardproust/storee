<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PasswordHash;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture {


    const MAX_CAT_NB = 8;
    const MAX_PRODUCTS_NB = 100;
    const MAX_USERS_NB = 10;

    private $slugger;
    private $hasher;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $hasher)
    {
        $this->slugger = $slugger;
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        // users

        $admin = new User();
        $admin
            ->setEmail('sygnostudio@pm.me')
            ->setFirstname('Sygno')
            ->setLastname('Studio')
            ->setPassword($this->hasher->hashPassword($admin, 'ev6]St64K6'))
            ->setRoles(['ROLE_ADMIN'])
            ->setCreatedAt(new \DateTime('yesterday'))
            ->setStreet($faker->streetAddress())
            ->setPostcode($faker->postcode())
            ->setCity($faker->city())
            ->setCountry("USA")
            ->setPhone($faker->phoneNumber());
        $manager->persist($admin);

        for($u = 1; $u <= self::MAX_USERS_NB; $u++) {
            $user = new User();
            $user
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setEmail(strtolower($user->getFirstname())."-".$user->getLastname()."@".$faker->freeEmailDomain())
                ->setPassword($this->hasher->hashPassword($user, strtolower($this->slugger->slug($user->getFirstname()))))
                ->setCreatedAt(new \DateTime('today'))
                ->setStreet($faker->streetAddress())
                ->setPostcode($faker->postcode())
                ->setCity($faker->city())
                ->setCountry("USA")
                ->setPhone($faker->phoneNumber());
            $manager->persist($user);
        }
    
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
                ->setSlug(strtolower($this->slugger->slug($cat->getName())));
            $manager->persist($cat);
            $categoryIDs[] = $cat;
        }

        // products

        for($p = 1; $p <= self::MAX_PRODUCTS_NB; $p++) {
            $product = new Product;
            $product
                ->setName($faker->productName())
                ->setPrice($faker->price())
                ->setSlug(strtolower($this->slugger->slug($product->getName())))
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