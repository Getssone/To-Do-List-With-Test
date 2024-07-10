<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route(path: '/homepage-old', name: 'homepage-old')]
    public function indexAction()
    {
        return $this->render('default/homepage-old.html.twig');
    }
}
