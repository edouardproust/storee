<?php namespace App\Controller\StaticPage;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TermsController extends AbstractController {

    /**
     * @Route("/terms", name="terms")
     */
    public function show(): Response  
    {
        return $this->render('staticPage/terms.html.twig');
    }

}