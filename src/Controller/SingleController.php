<?php namespace App\Controller;

use App\Form\ContactType;
use App\App\Component\Collection;
use App\App\Service\StripeService;
use App\App\Service\ContactService;
use App\Repository\ProductRepository;
use App\Event\ContactFormSubmittedEvent;
use App\Repository\AdminSettingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SingleController extends AbstractController {

    private $productRepository;
    private $settings;

    public function __construct(ProductRepository $productRepository, AdminSettingRepository $settings)
    {
        $this->productRepository = $productRepository;
        $this->settings = $settings;
    }

    /**
     * @Route("/", name="home")
     */
    public function home(): Response  
    {
        return $this->render('single/home.html.twig', [
            'lastProducts' => $this->productRepository->FindMostRecent(3),
            'popularProducts' => $this->productRepository->FindMostViewed(3)
        ]);
    }

    /**
     * @Route("/all-products/{page<\d+>?1}", name="catalog")
     */
    public function catalog($page): Response  
    {
        $collection = (new Collection($this->productRepository))
            ->build(
                $this->settings->get('productPerCollectionPage'), 
                $this->generateUrl('catalog'),
                $page,
                ["createdAt" => "DESC"]
            );
        if(@$collection['redirectToPage']) {
            $page = $collection['redirectToPage'];
            return $this->redirectToRoute('catalog', ['page' => $page]);
        }
        return $this->render('single/catalog.html.twig', [
            'collection' => $collection
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about(): Response 
    {
        return $this->render('single/about.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, EventDispatcherInterface $dispatcher, ContactService $contactService): Response  
    {
        $userInfo = $contactService->preFillForm();
        $form = $this->createForm(ContactType::class, $userInfo);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();
            $contactFormSubmittedEvent = new ContactFormSubmittedEvent($message);
            $dispatcher->dispatch($contactFormSubmittedEvent, 'contact.success');
        }
        return $this->render('single/contact.html.twig', [
            'contactForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/faq/{section?}", name="faq")
     */
    public function faq($section): Response  
    {
        return $this->render('single/faq.html.twig', [
            '_fragment' => $section
        ]);
    }

    /**
     * @Route("/terms", name="terms")
     */
    public function terms(): Response  
    {
        return $this->render('single/terms.html.twig');
    }

    /**
     * @Route("/privacy", name="privacy")
     */
    public function privacy(): Response  
    {
        return $this->render('single/privacy.html.twig');
    }

    /**
     * @Route("/not-found", name="404")
     */
    public function error404(): Response  
    {
        return $this->render('single/404.html.twig');
    }

    /**
     * @Route("/access-denied", name="access_denied")
     */
    public function accessDenied(): Response  
    {
        return $this->render('single/access-denied.html.twig');
    }

    /**
     * @Route("/test", name="test")
     */
    public function test(StripeService $stripeService): Response  
    {
        // Do testing here
        if(!$this->getUser() || !in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $this->addFlash('danger', 'Only admins can do developement tests.');
            return $this->redirectToRoute('home');
        }
        return $this->render('test.html.twig');
    }

}