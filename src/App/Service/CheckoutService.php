<?php

namespace App\App\Service;

use DateTime;
use App\Entity\User;
use App\Entity\Purchase;
use App\App\Entity\CartItem;
use App\Entity\PurchaseItem;
use App\App\Service\CartService;
use App\Repository\PurchaseRepository;
use App\Repository\DeliveryCountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CheckoutService extends AbstractController
{
    /** @var DeliveryCountryRepository */
    private $countryRepository;
    /** @var CartService */
    private $cartService;
    /** @var UserPasswordHasherInterface */
    private $hasher;
    private $purchaseRepository;

    public function __construct(
        DeliveryCountryRepository $countryRepository,
        CartService $cartService,
        PurchaseRepository $purchaseRepository,
        UserPasswordHasherInterface $hasher
    ){
        $this->countryRepository = $countryRepository;
        $this->cartService = $cartService;
        $this->purchaseRepository = $purchaseRepository;
        $this->hasher = $hasher;
    }

    /**
     * Tell if user is allowed to access to the given purchase or not.
     * Send a flash alert if not
     * @param Purchase $purchaseId 
     * @param null|string $redirectRoute 
     * @return null|RedirectResponse Redirects if not granted, or Purchase object if granted
     */
    public function accessGranted(Purchase $purchase): ?bool
    {
        $msg = 'Your were not allowed to pay for this order as ';
        if(!$purchase){
            $msg .= 'it does not exist';
        } elseif($this->getUser() !== $purchase->getUser()) {
            if(!$this->getUser()) {
                $msg .= 'you must be connected to access it. Please <a href="'.$this->generateUrl('security_login').'">login</a> first if you have an account.';
            } else {
                $msg .= 'your are not its author.';
            }
        } elseif($purchase->getStatus() === Purchase::STATUS_PAID) {
            $msg.= 'it is already paid';
        } elseif (count($this->cartService->getDetailedCart()) <= 0 && count($purchase->getPurchaseItems()) <= 0) {
            $msg.= 'it is empty.';
        } else {
            return true;
        }
        $this->addFlash('danger', $msg);
        return false;
    }

    public function createLinkWithReferer(string $targetRoute, string $refererRoute, array $parameters = []): string
    {
        $referer = $this->generateUrl($targetRoute, $parameters);
        $referer .= '?referer=' . $this->generateUrl($refererRoute);
        return $referer;
    }

    public function getPaymentStatusConst(string $status)
    {
        switch($status) {
            case 'pending':
                return Purchase::STATUS_PENDING; break;
            case 'paid':
                return Purchase::STATUS_PAID; break;
            default:
                throw new \Exception('Parameter for '.__METHOD__.' is not valid. Valid parameters are: \'paid\', \'pending\'.');
        }
    }

    /**
     * Used to pre-fill a form: returns an assoc. array filled with the logged-in user data.
     * @return array User data. Empty Array if user is not logged in.
     */
    public function getUserData(): array
    {
        /** @var User $user */
        if($user = $this->getUser()) {
            return [
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'street' => $user->getStreet(),
                'postcode' => $user->getPostcode(),
                'city' => $user->getCity(),
                'country' => $this->countryRepository->findOneBy(['code' => 'US']),
                'email' => $user->getEmail(),
                'phone' => $user->getPhone()
            ];
        }
        return [];
    }

    /**
     * Create a new Purchase and add basic infos to it 
     * and display logged in user's info in the checkout form (if it is passed as data in the formBuilder).
     * @param array $formData 
     * @return Purchase 
     */
    public function createPurchase(): Purchase
    {
        $user = $this->getUser();
        $purchase = new Purchase;
        // add purchaseItems
        $purchaseItems = $this->setPurchaseItems($purchase);
        foreach($purchaseItems as $item) {
            $purchase->addPurchaseItem($item);
        }
        // add date and total
        $purchase->setTotal($this->getTotal($purchase));
        // add user infos
        if($user) {
            $purchase
                ->setFirstname($user->getFirstname())
                ->setLastname($user->getLastname())
                ->setStreet($user->getStreet())
                ->setCity($user->getCity())
                ->setPostcode($user->getPostcode())
                ->setEmail($user->getEmail())
                ->setPhone($user->getPhone())
                ->setCountry($user->getCountry());
        }
        return $purchase;
    }

    public function updatePurchase(Purchase $purchase, ?User $user): void
    {
        $purchase
            ->setUser($user)
            ->setTotal($this->getTotal($purchase));
    }

    public function getTotalWithoutDelivery(Purchase $purchase): ?int
    {
        $productsTotal = 0;
        foreach($purchase->getPurchaseItems() as $item) {
            $productsTotal += $item->getTotal();
        }
        return $productsTotal;
    }

    private function getTotal(Purchase $purchase): ?int
    {
        $total = $this->getTotalWithoutDelivery($purchase);
        if(!$purchase->getDeliveryMethod()) return $total;
        return $total + $purchase->getDeliveryMethod()->getPrice();
    }

    /**
     * Build a user if password is set
     * @param array $formData 
     * @return User 
     */
    public function createUser(Purchase $purchase): ?User
    {
        $user = (new User)
            ->setEmail($purchase->getEmail())
            ->setFirstname($purchase->getFirstname())
            ->setLastname($purchase->getLastname())
            ->setPhone($purchase->getPhone())
            ->setStreet($purchase->getStreet())
            ->setCity($purchase->getCity())
            ->setPostcode($purchase->getPostcode())
            ->setCountry($purchase->getCountry());
        $hashedPassword = $this->hasher->hashPassword($user, $purchase->getPassword());
        $user->setPassword($hashedPassword);
        
        return $user;
    }

    /**
     * @param Purchase $purchase 
     * @return array 
     */
    private function setPurchaseItems(Purchase $purchase): array
    {
        $purchaseItems = [];
        $detailedCart = $this->cartService->getDetailedCart();
        foreach($detailedCart as $item){
            /** @var CartItem $item */
            $purchaseItem = (new PurchaseItem)
                ->setPurchase($purchase)
                ->setProduct($item->getProduct())
                ->setQuantity($item->getQuantity())
                ->setTotal($item->getTotal());
            $purchaseItems[] = $purchaseItem;
        }
        return $purchaseItems;
    }

}