<?php namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SingleController extends AbstractController {

    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/", name="home")
     */
    public function home(): Response  
    {
        $products = $this->productRepository->findBy([], ["createdAt" => "DESC"], 3);
        return $this->render('single/home.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/all-products", name="catalog")
     */
    public function catalog(): Response  
    {
        $products = $this->productRepository->findBy([], ["createdAt" => "DESC"]);
        return $this->render('single/catalog.html.twig', [
            'products' => $products
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
    public function contact(): Response  
    {
        return $this->render('single/contact.html.twig');
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
     * @Route("/test", name="test")
     */
    public function test(ValidatorInterface $validator): Response  
    {
        // Do testing here
        return $this->render('test.html.twig');
    }

}