<?php 

namespace App\App\Service;

use Stripe\Stripe;
use App\Entity\Purchase;
use Stripe\PaymentIntent;
use App\App\Service\CheckoutService;

class StripeService 
{

    private $secretKey;

    public function __construct(string $publicKey, string $secretKey, CheckoutService $checkoutService)
    {
        $this->publicKey = $publicKey;
        $this->secretKey = $secretKey;
        $this->checkoutService = $checkoutService;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    public function getClientSecret(Purchase $purchase): string
    {
        return $this->createPaymentIntent($purchase)->client_secret;
    }

    /**
     * @param int $total Price to pay by client
     * @return PaymentIntent
     */
    private function createPaymentIntent(Purchase $purchase): PaymentIntent
    {
        // This is a sample test API key.
        Stripe::setApiKey($this->getSecretKey());

        // Create a PaymentIntent with amount and currency
        return PaymentIntent::create([
            'amount' => $purchase->getTotal(),
            'currency' => 'eur',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);
    }

}