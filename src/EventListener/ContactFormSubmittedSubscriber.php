<?php

namespace App\EventListener;

use App\App\Service\AdminSettingService;
use App\App\Service\EmailService;
use Symfony\Component\Mime\Address;
use Psr\Container\ContainerInterface;
use App\Event\ContactFormSubmittedEvent;
use App\Repository\AdminSettingRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ContactFormSubmittedSubscriber implements EventSubscriberInterface
{

    /** @var MailerInterface */
    private $mailer;
    
    /** @var AdminSettingService */
    private $adminSettingService;

    /** @var EmailService */
    private $emailService;
    

    public function __construct(MailerInterface $mailer, AdminSettingService $adminSettingService, EmailService $emailService)
    {
        $this->mailer = $mailer;
        $this->adminSettingService = $adminSettingService;
        $this->emailService = $emailService;
    }

    public static function getSubscribedEvents()
    {
        return [
            'contact.success' => 'sendEmail'
        ];
    }

    public function sendEmail(ContactFormSubmittedEvent $event): void
    {
        $message = $event->getContactMessage();
        $s = $this->adminSettingService;
        try {
            $email = (new TemplatedEmail)
                ->to(new Address($message['email'], $message['fullname']))
                ->from(new Address($s->getValue('storeEmail'), $s->getValue('storeEmailExpeditor')))
                ->subject('New contact message from ' . $s->getValue('siteName'))
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    'contactMessage' => $message
                ]);
            $this->mailer->send($email);
            $this->emailService->successFlash();
        } catch (\Exception $e) {
            $this->emailService->failureFlash($e);
        }
    }

}