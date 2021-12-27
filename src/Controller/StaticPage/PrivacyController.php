<?php namespace App\Controller\StaticPage;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PrivacyController extends AbstractController {

    /**
     * @Route("/privacy", name="privacy")
     */
    public function show(): Response  
    {
        return $this->render('staticPage/privacy.html.twig');
    }

}