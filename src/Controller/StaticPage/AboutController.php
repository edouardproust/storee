<?php 

namespace App\Controller\StaticPage;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AboutController extends AbstractController {

    /**
     * @Route("/about", name="about")
     */
    public function about(): Response 
    {
        return $this->render('staticPage/about.html.twig');
    }

}