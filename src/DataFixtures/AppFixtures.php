<?php

namespace App\DataFixtures;

use App\App\Helper\DeliveryHelper;
use App\Entity\{
    User,
    Product,
    Category,
    DeliveryCountry,
    Purchase,
    PurchaseItem,
    DeliveryMethod,
    PaymentMethod
};
use Doctrine\{
    Persistence\ObjectManager,
    Bundle\FixturesBundle\Fixture
};
use Symfony\Component\{
    String\Slugger\SluggerInterface,
    PasswordHasher\Hasher\UserPasswordHasherInterface
};

class AppFixtures extends Fixture {

    const ADMIN_USERNAME = "admin";
    const ADMIN_PASSWORD = "admin";

    const CATEGORIES = 10;
    const PRODUCTS = 27;
    const USERS = 34;
    const PURCHASES = 41;
    const MAX_ROWS_PER_PURCHASE = 8;
    const MAX_QTY_PER_ROW = 5;
    const DELIVERY_METHODS = [
        'USPS' => ['Normal - 72h', 490, ['US' => "United States"]], 
        'FedEx' => ['Quick - 48h', 1390], 
        'UPS' => ['Express - 24h', 2690]
    ];
    const PAYMENT_METHODS = ["Stripe"];

    private $slugger;
    private $hasher;
    private $faker;

    /** @var User[] */
    private $users = [];

    /** @var Product[] */
    private $products = [];

    /** @var Category[] */
    private $categories = [];

    /** @var Purchase[] */
    private $purchases = [];

    /** @var PurchaseItem[] */
    private $purchaseItems = [];

    /** @var DeliveryCountry */
    private $deliveryCountries = [];

    /** @var DeliveryMethod[] */
    private $deliveryMethods;

    /** @var PaymentMethod */
    private $paymentMethods = [];

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $hasher)
    {
        $this->slugger = $slugger;
        $this->hasher = $hasher;
        $this->faker = \Faker\Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        // load faker providers
        $this->faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($this->faker));
        $this->faker->addProvider(new \Liior\Faker\Prices($this->faker));
        $this->faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($this->faker));

        // create entities
        $this->createUsers();
        $this->createCategories();
        $this->createProducts();
        $this->createPurchases();
        $this->createPurchaseItems();
        $this->createDeliveryCountries();
        $this->createDeliveryMethods();
        $this->createPaymentMethods();
        
        // save entities in an array
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
        foreach($this->deliveryCountries as $deliveryCountry) {
            $manager->persist($deliveryCountry);
        }
        foreach($this->deliveryMethods as $deliveryMethod) {
            $manager->persist($deliveryMethod);
        }
        foreach($this->paymentMethods as $paymentMethod) {
            $manager->persist($paymentMethod);
        }

        $manager->flush();
    }
    
    private function createUsers(): void
    {
        // admin
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
            $purchase->setUserData(serialize($purchase->getUser()));
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
                    ->setTotal($purchaseItem->getProduct()->getPrice() * $purchaseItem->getQuantity());
                $purchaseItem
                    ->setProductData(serialize($purchaseItem->getProduct()));
                $this->purchaseItems[] = $purchaseItem;

                $purchaseTotal += $purchaseItem->getTotal();
            }
            $purchase->setTotal($purchaseTotal);
        }
    }

    private function createDeliveryCountries(): void
    {
        foreach(DeliveryHelper::deliveryCountries() as $code => $name) {
            $country = (new DeliveryCountry)
                ->setCode($code)
                ->setName($name);
            $this->deliveryCountries[] = $country;
        }
    }

    private function createDeliveryMethods(): void
    {
        foreach(self::DELIVERY_METHODS as $carrier => $infos) {
            $method = (new DeliveryMethod)
                ->setName($infos[0])
                ->setCarrier($carrier)
                ->setPrice($infos[1]);
                foreach($this->deliveryCountries as $country) {
                    $method->addCountry($country);
                }
            $this->deliveryMethods[] = $method;
        }
    }

    private function createPaymentMethods(): void
    {
        foreach(self::PAYMENT_METHODS as $methodName) {
            $method = (new PaymentMethod)
                ->setName($methodName);
            $this->paymentMethods[] = $method;
        }
    }

}