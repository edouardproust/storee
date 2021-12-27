<?php

namespace App\EventListener;

use App\App\Service\AdminSettingService;
use Exception;
use App\App\Service\EmailService;
use App\Event\PurchaseSuccessEvent;
use App\Repository\AdminSettingRepository;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchaseSuccessEmailSubscriber extends EmailService implements EventSubscriberInterface
{

    /** @var MailerInterface */
    private $mailer;

    /** @var EmailService */
    private $emailService;

    /** @var AdminSettingService */
    private $adminSettingService;

    public function __construct(MailerInterface $mailer, EmailService $emailService, AdminSettingService $adminSettingService)
    {
        $this->mailer = $mailer;
        $this->emailService = $emailService;
        $this->adminSettingService = $adminSettingService;
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
        $s = $this->adminSettingService;
        try {
            $email = (new TemplatedEmail)
                ->to(new Address($purchase->getEmail(), $purchase->getFirstname() . ' ' . $purchase->getLastname()))
                ->from(new Address($s->getValue('storeEmail'), $s->getValue('storeEmailExpeditor')))
                ->subject('Order nr. ' . $purchase->getId() . ' confirmed' )
                ->htmlTemplate('emails/purchase-success.html.twig')
                ->context([
                    'purchase' => $purchase
                ]);
            $this->mailer->send($email);
        } catch (Exception $e) {
            $this->emailService->failureFlash($e, "Confirmation email");
        }
    }

}