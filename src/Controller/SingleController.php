<?php namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SingleController extends AbstractController {

    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/", name="home")
     */
    public function home() {
        $products = $this->productRepository->findBy([], ["createdAt" => "DESC"], 3);
        return $this->render('single/home.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/all-products", name="catalog")
     */
    public function catalog() {
        $products = $this->productRepository->findBy([], ["createdAt" => "DESC"]);
        return $this->render('single/catalog.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about() {
        return $this->render('single/about.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact() {
        return $this->render('single/contact.html.twig');
    }

    /**
     * @Route("/faq/{section?}", name="faq")
     */
    public function faq($section) {
        return $this->render('single/faq.html.twig', [
            'section' => $section
        ]);
    }

    /**
     * @Route("/terms", name="terms")
     */
    public function terms() {
        return $this->render('single/terms.html.twig');
    }

    /**
     * @Route("/privacy", name="privacy")
     */
    public function privacy() {
        return $this->render('single/privacy.html.twig');
    }

    /**
     * @Route("/page-not-found", name="404")
     */
    public function error404() {
        return $this->render('single/404.html.twig');
    }

    /**
     * @Route("/test", name="test")
     */
    public function test(ProductRepository $productRepo) {
        $faker = \Faker\Factory::create();
        $faker->addProvider((new \Bezhanov\Faker\Provider\Commerce($faker)));
        $categories = $faker->category();
        dump($categories);
        return $this->render('test.html.twig');
    }

}