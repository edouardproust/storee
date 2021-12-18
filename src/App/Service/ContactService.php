<?php

namespace App\App\Service;

use App\Entity\User;
use App\App\Entity\ContactMessage;
use Symfony\Component\Security\Core\Security;

class ContactService
{

    /** @var Security */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /** 
     * Pre-fill some form fields with the user informations if he/she is connected
     * @return array  
     * 
    */
    public function preFillForm()
    {
        $message = [];
        /** @var User */
        $user = $this->security->getUser();
        if(!empty($user)) {
            $message['fullname'] = $user->getFirstname() . ' ' . $user->getLastname();
            $message['email'] = $user->getEmail();
            $message['phone'] = $user->getPhone();
        }
        return $message;
    }

}