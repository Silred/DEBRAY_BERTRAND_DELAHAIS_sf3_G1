<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Article');

        $list = $repository->findAll();

        $content = $this
            ->get('templating')
            ->render('AppBundle:Default:index.html.twig',
                array(
                    'Articles' => $list
                ));

        return new Response($content);
    }
}
