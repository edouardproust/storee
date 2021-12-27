<?php namespace App\Controller\StaticPage;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController {

    /**
     * @Route("/test", name="test")
     */
    public function show(): Response  
    {
        // Do testing here
        if(!$this->getUser() || !in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $this->addFlash('danger', 'Only admins can do developement tests.');
            return $this->redirectToRoute('home');
        }
        return $this->render('test.html.twig');
    }

}