<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    /**
     * The above function is a PHP controller method for rendering the homepage with a summary comment.
     * 
     * @return Response The `index()` method is returning a response that renders the
     * `default/index.html.twig` template with the controller name set to 'HomePage'. The commented out
     * line `->addFlash('success', 'Good! Operation successful.');` suggests that a flash message
     * was intended to be added but is currently disabled.
     */
    #[Route('/homepage', name: 'homepage')]
    public function index(): Response
    {
        $this->addFlash('success', 'Bienvenu dans votre Todo List');

        return $this->render('pages/default/index.html.twig', [
            'controller_name' => 'HomePage',
        ]);
    }
}
