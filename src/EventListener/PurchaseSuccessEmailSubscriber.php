<?php

namespace App\EventListener;

use Exception;
use App\App\Config;
use App\Event\PurchaseSuccessEvent;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{

    /** @var MailerInterface */
    private $mailer;

    /** @var ContainerInterface */
    private $container;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        MailerInterface $mailer, 
        ContainerInterface $container, 
        UrlGeneratorInterface $urlGenerator)
    {
        $this->mailer = $mailer;
        $this->container = $container;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents()
    {
        return [
            'purchase.success' => 'sendEmail'
        ];
    }

    public function sendEmail(PurchaseSuccessEvent $purchaseSuccessEvent): void
    {
        $purchase = $purchaseSuccessEvent->getPurchase();
        try {
            $email = (new TemplatedEmail)
                ->to(new Address($purchase->getEmail(), $purchase->getFirstname() . ' ' . $purchase->getLastname()))
                ->from(new Address(Config::CONTACT_EMAIL, Config::CONATCT_NAME))
                ->subject('Order nr. ' . $purchase->getId() . ' confirmed' )
                ->htmlTemplate('emails/purchase-success.html.twig')
                ->context([
                    'purchase' => $purchase
                ]);
            $this->mailer->send($email);
        } catch (Exception $e) {
            $this->container->get('request_stack')->getSession()->getFlashBag()->add('danger', 
                'Confirmation email could not be sent.<br>' . 
                'Please <a href="' . $this-> urlGenerator->generate('contact'). '" target="_blank">Contact us here</a>' . 
                ' by sending us the following error message: <i>' . $e->getMessage() . '</i>'
            );
        }
    }

}