<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    
    #[Route('/', name: 'home')]
    public function index()
    {
        /**
         *  En este controlador, configuramos la página de inicio
         *  de nuestra aplicación Symfony 6
         */

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
