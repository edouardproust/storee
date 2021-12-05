<?php 

namespace App\App\Service;

use App\App\Service\CartService as CartCartService;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeService 
{

    private $secretKey;
    private $cartService;

    public function __construct(string $secretKey, CartCartService $cartService)
    {
        $this->secretKey = $secretKey;
        $this->cartService = $cartService;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    public function getClientSecret(): string
    {
        return $this->createPaymentIntent()->client_secret;
    }

    /**
     * @param int $total Price to pay by client
     * @return PaymentIntent
     */
    private function createPaymentIntent(): PaymentIntent
    {
        // This is a sample test API key.
        Stripe::setApiKey($this->getSecretKey());

        // Create a PaymentIntent with amount and currency
        return PaymentIntent::create([
            'amount' => $this->cartService->getTotal(),
            'currency' => 'eur',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);
    }

}