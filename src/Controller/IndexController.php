<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class IndexController
 */
class IndexController  extends AbstractController
{
    /**
     * @Route ("/", name="index")
     *
     * @return Response
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }
}
