<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture {


    const CATEGORIES = 10;
    const PRODUCTS = 27;
    const USERS = 34;
    const PURCHASES = 41;
    const MAX_ROWS_PER_PURCHASE = 8;
    const MAX_QTY_PER_ROW = 5;

    private $slugger;
    private $hasher;
    private $faker;

    /** @var User[] */
    private $users;

    /** @var Product[] */
    private $products;

    /** @var Category[] */
    private $categories;

    /** @var Purchase[] */
    private $purchases;

    /** @var PurchaseItem[] */
    private $purchaseItems;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $hasher)
    {
        $this->slugger = $slugger;
        $this->hasher = $hasher;
        $this->faker = \Faker\Factory::create();

        $this->users = [];
        $this->products = [];
        $this->categories = [];
        $this->purchases = [];
        $this->purchaseItems = [];
    }

    public function load(ObjectManager $manager): void
    {
        $this->faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($this->faker));
        $this->faker->addProvider(new \Liior\Faker\Prices($this->faker));
        $this->faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($this->faker));

        $this->createUsers();
        $this->createCategories();
        $this->createProducts();
        $this->createPurchases();
        $this->createPurchaseItems();

        foreach($this->users as $user) {
           $manager->persist($user); 
        }
        foreach($this->products as $product) {
            $manager->persist($product);
        }
        foreach($this->categories as $category) {
            $manager->persist($category);
        }
        foreach($this->purchases as $purchase) {
            $manager->persist($purchase);
        }
        foreach($this->purchaseItems as $purchaseItem) {
            $manager->persist($purchaseItem);
        }

        $manager->flush();
    }
    
    private function createUsers(): void
    {
        // admin
        $admin = new User();
        $admin
            ->setEmail('sygnostudio@pm.me')
            ->setFirstname('Sygno')
            ->setLastname('Studio')
            ->setPassword($this->hasher->hashPassword($admin, 'ev6]St64K6'))
            ->setRoles(['ROLE_ADMIN'])
            ->setCreatedAt(new \DateTime('yesterday'))
            ->setStreet($this->faker->streetAddress())
            ->setPostcode($this->faker->postcode())
            ->setCity($this->faker->city())
            ->setCountry("USA")
            ->setPhone($this->faker->phoneNumber());
        $this->users[] = $admin;

        // other users
        for($u = 1; $u <= self::USERS; $u++) {
            $user = new User();
            $user
                ->setFirstname($this->faker->firstName())
                ->setLastname($this->faker->lastName())
                ->setEmail(strtolower($user->getFirstname()."-".$user->getLastname())."@".$this->faker->freeEmailDomain())
                ->setPassword($this->hasher->hashPassword($user, strtolower($user->getFirstname())))
                ->setCreatedAt(new \DateTime('today'))
                ->setStreet($this->faker->streetAddress())
                ->setPostcode($this->faker->postcode())
                ->setCity($this->faker->city())
                ->setCountry("USA")
                ->setPhone($this->faker->phoneNumber());
            $this->users[] = $user;
        }
    }
        
    private function createCategories(): void
    {
        // Category "Undefined"
        $undefined = new Category;
        $undefined
            ->setName("Undefined")
            ->setSlug("undefined");
        $this->categories[] = $undefined;

        // Other categories
        $categoryNames = [];
        for($c = 1; $c <= self::CATEGORIES; $c++) {
            $category = new Category;
            // check that each category name is unique
            $fakeCatName = $this->faker->category();
            while(in_array($fakeCatName, $categoryNames)) {
                $fakeCatName = $this->faker->category();
            }
            $categoryNames[] = $fakeCatName;
            // register category
            $category
                ->setName($fakeCatName)
                ->setSlug(strtolower($this->slugger->slug($category->getName())));
            $this->categories[] = $category;
        }
    }

    private function createProducts(): void
    {
        for($p = 1; $p <= self::PRODUCTS; $p++) {
            $product = new Product;
            $product
                ->setName($this->faker->productName())
                ->setPrice($this->faker->price())
                ->setSlug(strtolower($this->slugger->slug($product->getName())))
                ->setShortDescription($this->faker->paragraph())
                ->setMainImage($this->faker->imageUrl(600, 450, true))
                ->setCreatedAt($this->faker->dateTimeBetween('-1 year', 'now'))
                ->setCategory($this->faker->randomElement($this->categories));
            $this->products[] = $product;
        }
    } 


    private function createPurchases(): void
    {
        for($p = 1; $p <= self::PURCHASES; $p++) {
            $purchase = new Purchase;
            $purchase
                ->setUser($this->faker->randomElement($this->users))
                ->setCreatedAt($this->faker->dateTimeBetween('-6 months'));
                // ->setStatus(Purchase::STATUS_PENDING);
                // ->setTotal(); -> set in createPurchaseItems()
            
                $user = $purchase->getUser();

            $userData = [
                "email" => $user->getEmail(),
                "firstname" => $user->getFirstname(),
                "lastname" => $user->getLastname(),
                "street" => $user->getStreet(),
                "postcode" => $user->getPostcode(),
                "city" => $user->getCity(),
                "country" => $user->getCountry(),
                "phone" => $user->getPhone(),
                "created_at" => $user->getCreatedAt()
            ];
            $purchase->setUserData(json_encode($userData));
            $this->purchases[] = $purchase;
        }
    }

    private function createPurchaseItems(): void
    {
        foreach($this->purchases as $purchase) {
            $purchaseItems = mt_rand(1, self::MAX_ROWS_PER_PURCHASE);

            $purchaseTotal = 0;
            
            for($pi = 1; $pi <= $purchaseItems; $pi++) {
                $purchaseItem = new PurchaseItem;
                $purchaseItem
                    ->setPurchase($purchase)
                    ->setProduct($this->faker->randomElement($this->products))
                    ->setQuantity(mt_rand(1, self::MAX_QTY_PER_ROW))
                    ->setTotal($purchaseItem->getProduct()->getPrice() * $purchaseItem->getQuantity())
                    ->setProductName($purchaseItem->getProduct()->getName())
                    ->setProductPrice($purchaseItem->getProduct()->getPrice());
                $this->purchaseItems[] = $purchaseItem;

                $purchaseTotal += $purchaseItem->getTotal();
            }
            $purchase->setTotal($purchaseTotal);
        }
    }

}