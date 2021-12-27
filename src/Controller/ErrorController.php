<?php namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ErrorController extends AbstractController {

    public function error404(): void
    {
        /*
            Template located in: 
             templates/bundles/TwigBundle/Exception/error404.html.twig
        */
    }

    /**
     * @Route("/access-denied", name="access_denied")
     */
    public function accessDenied(): Response  
    {
        return $this->render('error/access-denied.html.twig');
    }

}