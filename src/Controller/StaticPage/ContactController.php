<?php 

namespace App\Controller\StaticPage;

use App\Form\ContactType;
use App\App\Service\ContactService;
use App\Event\ContactFormSubmittedEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController {

    public function __construct(EventDispatcherInterface $dispatcher, ContactService $contactService)
    {
        $this->dispatcher = $dispatcher;
        $this->contactService = $contactService;
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function show(Request $request): Response  
    {
        $userInfo = $this->contactService->preFillForm();
        $form = $this->createForm(ContactType::class, $userInfo);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();
            $contactFormSubmittedEvent = new ContactFormSubmittedEvent($message);
            $this->dispatcher->dispatch($contactFormSubmittedEvent, 'contact.success');
        }
        return $this->render('staticPage/contact.html.twig', [
            'contactForm' => $form->createView()
        ]);
    }

}