<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ContactFormSubmittedEvent extends Event
{

    private $contactMessage;

    public function __construct(array $contactMessage)
    {
        $this->contactMessage = $contactMessage;
    }

    public function getContactMessage(): ?array
    {
        return $this->contactMessage;
    }

}