<?php

namespace App\App\Service;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailService {

    /** @var FlashBagInterface */
    private $flashBag;

    public function __construct(FlashBagInterface $flashBag, UrlGeneratorInterface $urlGenerator)
    {
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
    }

    public function failureFlash(\Exception $exception, string $emailType = "Email")
    {
        $this->flashBag->add('danger', 
            $emailType . ' could not be sent.<br>' . 
            'Please <a href="' . $this->urlGenerator->generate('contact'). '" target="_blank">Contact us here</a>' . 
            ' by sending us the following error message: <i>' . $exception->getMessage() . '</i>'
        );
    }

    public function successFlash()
    {
        $this->flashBag->add('success', 
            'Your message as been sent. A member of our team will write you back as soon as possible.'
        );
    }

}