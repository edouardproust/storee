<?php 

namespace App\DataFixtures;

use App\App\Helper\DeliveryHelper;
use Faker\Factory;
use App\Entity\{
    User,
    Upload,
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
};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/** 
 * Load fixtures for DEV environnement.
 * Use console command: "php bin/console doctrine: fixtures:load --env dev"
 */
class DevFixtures extends AbstractFixture 
{

    const ADMIN_USERNAME = "admin";
    const ADMIN_PASSWORD = "admin";

    const CATEGORIES = 10;
    const PRODUCTS = 77;
    const USERS = 34;
    /** Must match a $value in App\App\Helper\DeliveryHelper::worldCountries() */
    const USERS_COUNTRY= 'United States'; 
    const PURCHASES = 41;
    const PURCHASE_MAX_ROWS = 8;
    const PURCHASE_REGISTERED_USERS_PERCENT = 80;
    const PURCHASE_PAID_PERCENT = 90;
    const MAX_QTY_PER_ROW = 5;
    const DELIVERY_METHODS = [
        'USPS' => ['Normal - 72h', 490, ['US' => "United States"]], 
        'FedEx' => ['Quick - 48h', 1390], 
        'UPS' => ['Express - 24h', 2690]
    ];
    const PAYMENT_METHODS = ["Stripe"];

    private $hasher;
    private $faker;

    /** @var User[] */
    private $users = []; // contains Admin too

    /** @var Upload[] */
    private $productMainImages = [];

    /** @var Product[] */
    private $products = [];

    /** @var Category[] */
    private $categories = [];

    /** @var DeliveryCountry */
    private $deliveryCountries = [];

    /** @var DeliveryMethod[] */
    private $deliveryMethods;

    /** @var PaymentMethod */
    private $paymentMethods = [];

    /** @var Purchase[] */
    private $purchases = [];

    /** @var PurchaseItem[] */
    private $purchaseItems = [];

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
        $this->faker = Factory::create();
    }

    protected function getEnvironments()
    {
        return ['dev'];
    }

    protected function envLoad(ObjectManager $manager): void
    {
        // load faker providers
        $this->faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($this->faker));
        $this->faker->addProvider(new \Liior\Faker\Prices($this->faker));
        $this->faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($this->faker));

        // create entities
        $this->createAdmin();
        $this->createUsers();
        $this->createCategories();
        $this->createProductMainImages();
        $this->createProducts();
        $this->createDeliveryCountries();
        $this->createDeliveryMethods();
        $this->createPaymentMethods();
        $this->createPurchases();
        $this->createPurchaseItems();
        
        // persist
        foreach($this->productMainImages as $mainImage) {
            $manager->persist($mainImage);
         }
        foreach($this->users as $user) {
           $manager->persist($user); // contains admin too
        }
        foreach($this->products as $product) {
            $manager->persist($product);
        }
        foreach($this->categories as $category) {
            $manager->persist($category);
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
        foreach($this->purchases as $purchase) {
            $manager->persist($purchase);
        }
        foreach($this->purchaseItems as $purchaseItem) {
            $manager->persist($purchaseItem);
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
            ->setCountry(self::USERS_COUNTRY)
            ->setPhone($this->faker->phoneNumber());
        $this->users[] = $admin;
    }
    
    /**
     * Create all users
     * @return void 
     */
    private function createUsers(): void
    {
        for($u = 1; $u <= self::USERS; $u++) {
            $user = $this->createOneUser(true);
            $this->users[] = $user;
        }
    }

    /**
     * Create one user
     * @param bool $setPassword Use False only for entities that will not be persisted. Use True (default) otherwise.
     * @return void 
     */
    private function createOneUser(bool $setPassword = true): User
    {
        $user = new User();
        $user
            ->setFirstname($this->faker->firstName())
            ->setLastname($this->faker->lastName())
            ->setEmail(strtolower($user->getFirstname()."-".$user->getLastname())."@".$this->faker->freeEmailDomain())
            ->setCreatedAt(new \DateTime('today'))
            ->setStreet($this->faker->streetAddress())
            ->setPostcode($this->faker->postcode())
            ->setCity($this->faker->city())
            ->setCountry(self::USERS_COUNTRY)
            ->setPhone($this->faker->phoneNumber());
        if($setPassword) {
            $user->setPassword($this->hasher->hashPassword($user, strtolower($user->getFirstname())));
        } 
        return $user;
    }
    
    private function createCategories(): void
    {
        // Category "Undefined"
        $undefined = new Category;
        $undefined
            ->setName("Undefined");
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
                ->setDescription($this->faker->paragraph());
            $this->categories[] = $category;
        }
    }

    private function createProductMainImages(): void
    {
        for($p = 1; $p <= self::PRODUCTS; $p++) {
            $mainImage = new Upload;
            $mainImage
                ->setName("fixture-product-$p-mainImage")
                ->setUrl($this->faker->imageUrl(600, 450, true));
                $this->productMainImages[] = $mainImage;
        }
    }

    private function createProducts(): void
    {
        for($p = 1; $p <= self::PRODUCTS; $p++) {
            $product = new Product;
            $product
                ->setName($this->faker->productName())
                ->setPrice($this->faker->price())
                ->setShortDescription($this->faker->paragraph())
                ->setMainImage($this->productMainImages[$p-1])
                ->setCategory($this->faker->randomElement($this->categories))
                ->setViews(mt_rand(0, 300))
                ->setCreatedAt($this->faker->dateTimeBetween('-1 year', 'today'));
            $this->products[] = $product;
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
            $method = (new PaymentMethod)->setName($methodName);
            $this->paymentMethods[] = $method;
        }
    }

    private function createPurchases(): void
    {
        for($p = 1; $p <= self::PURCHASES; $p++) {
            $purchase = new Purchase;
            $isRegisteredUser = $this->faker->boolean(self::PURCHASE_REGISTERED_USERS_PERCENT);
            if($isRegisteredUser) {
                $user = $this->faker->randomElement($this->users);
                $purchase->setUser($user);
            } else {
                $user = $this->createOneUser(false);
            }
            $purchase
                ->setFirstname($user->getFirstname())
                ->setLastname($user->getLastname())
                ->setPassword($user->getPassword())
                ->setStreet($user->getStreet())
                ->setPostcode($user->getPostcode())
                ->setCity($user->getCity())
                ->setCountry($user->getCountry())
                ->setEmail($user->getEmail())
                ->setPhone($user->getPhone())
                ->setCreatedAt($this->faker->dateTimeBetween('-6 months'))
                ->setDeliveryMethod($this->faker->randomElement($this->deliveryMethods))
                ->setPaymentMethod($this->faker->randomElement($this->paymentMethods));
            $isPaid = $this->faker->boolean(self::PURCHASE_PAID_PERCENT);
            if($isPaid) {
                $purchase->setStatus(Purchase::STATUS_PAID);
            }
            $this->purchases[] = $purchase;
        }
    }

    private function createPurchaseItems(): void
    {
        foreach($this->purchases as $purchase) {
            $purchaseItems = mt_rand(1, self::PURCHASE_MAX_ROWS);
            for($pi = 1; $pi <= $purchaseItems; $pi++) {
                $purchaseItem = new PurchaseItem;
                $product = $this->faker->randomElement($this->products);
                $purchaseItem
                    ->setPurchase($purchase)
                    ->setProduct($product)
                    ->setQuantity(mt_rand(1, self::MAX_QTY_PER_ROW));
                $purchase->addPurchaseItem($purchaseItem);
                $this->purchaseItems[] = $purchaseItem;
                // add a purchase to the product
                $product->addPurchase($purchaseItem->getQuantity());
            }
        }
    }

}