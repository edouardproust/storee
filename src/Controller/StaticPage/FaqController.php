<?php namespace App\Controller\StaticPage;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FaqController extends AbstractController {

    /**
     * @Route("/faq/{section?}", name="faq")
     */
    public function show($section): Response  
    {
        return $this->render('staticPage/faq.html.twig', [
            '_fragment' => $section
        ]);
    }

}