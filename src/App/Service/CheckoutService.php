<?php

namespace App\App\Service;

use DateTime;
use App\Entity\Purchase;
use App\App\Entity\CartItem;
use App\Entity\PurchaseItem;
use App\Entity\User;
use App\App\Service\CartService;
use App\Repository\DeliveryCountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckoutService extends AbstractController
{
    /** @var DeliveryCountryRepository */
    private $countryRepository;

    /** @var CartService */
    private $cartService;

    public function __construct(
        DeliveryCountryRepository $countryRepository,
        CartService $cartService
    ){
        $this->countryRepository = $countryRepository;
        $this->cartService = $cartService;
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

    public function setPurchase(array $formData): Purchase
    {
        $user = $this->getUser();

        // misc.
        $purchase = (new Purchase)
            ->setDelivery($formData['method'])
            ->setTotal($formData['total'])
            ->setUser($user)
            ->setCreatedAt(new DateTime);
        // userData
        if(is_object($formData['country'])) {
            $formData['country'] = $formData['country']->getName();
        } 
        $userData = serialize($formData);
        $purchase->setUserData($userData);
        // purchaseItems
        $purchaseItems = $this->setPurchaseItems($purchase);
        foreach($purchaseItems as $item) {
            $purchase->addPurchaseItem($item);
        }

        return $purchase;
    }

    private function setPurchaseItems(Purchase $purchase): array
    {
        $purchaseItems = [];
        $detailedCart = $this->cartService->getDetailedCart();
        foreach($detailedCart as $item){
            /** @var CartItem $item */
            $purchaseItem = (new PurchaseItem)
                ->setPurchase($purchase)
                ->setProduct($item->getProduct())
                ->setQuantity($item->getQty())
                ->setTotal($item->getTotal())
                ->setProductData(serialize($item->getProduct()));
            $purchaseItems[] = $purchaseItem;
        }
        return $purchaseItems;
    }

}