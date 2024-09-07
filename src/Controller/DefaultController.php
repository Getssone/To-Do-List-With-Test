<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DefaultController extends AbstractController
{

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/homepage', name: 'homepage')]
    public function homepage(): Response
    {
        $this->addFlash('success', 'Bienvenu dans votre Todo List');

        return $this->render('pages/default/homepage.html.twig', [
            'controller_name' => 'HomePage',
        ]);
    }

    /**
     * @codeCoverageIgnore
     */
    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/homepage-old', name: 'homepage-old')]
    public function homepageOld(): Response
    {
        $this->addFlash('success', 'Bienvenu dans votre Todo List');

        return $this->render('pages/default/homepage-old.html.twig', [
            'controller_name' => 'HomePage',
        ]);
    }
}
