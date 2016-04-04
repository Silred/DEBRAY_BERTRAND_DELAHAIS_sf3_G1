<?php

namespace AppBundle\Controller\Article;

use AppBundle\Entity\Article;
use AppBundle\Entity\Tag;
use AppBundle\Form\TagType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ArticleController extends Controller
{
    /**
     * @Route("/add")
     *
     */
    public function addAction(Request $request)
    {
        $article = new Article();

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class)
            ->add('author', TextType::class)
            ->add('content', TextareaType::class)
            ->add('tags' , CollectionType::class , array(
                'entry_type'   => TagType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'entry_options'  => array(
                    'required'  => false,
                    'attr'      => array('class' => 'Tag')
                ),
            ))
            ->add('save', SubmitType::class, array('label' => 'Créer article'))
            ->getForm();

        $form->handleRequest($request);

        if($form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('app_home_index');
        }

        return $this->render('AppBundle:Article:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }



    /**
     * @Route("/{id}", requirements={"id" = "\d+"})
     *
     * @param $id
     *
     * @return Response
     */
    public function showAction($id, Request $request)
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Article');

        $article = $repository->find($id);

        $content = $this
            ->get('templating')
            ->render('AppBundle:Article:show.html.twig',
                array(
                    'article' => $article
                ));

        return new Response($content);
    }

    /**
     * @Route("/list/{tag}"))
     *
     * @param $tag
     *
     * @return Response
     */
    public function listAction($tag, Request $request)
    {

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Article');

        $articles = $repository->findAll();

        $lists = array();

        $i = 0;

        if ($tag == 'all') {
            foreach ($articles as $article) {
                $lists[$i] = $article;

                $i = $i + 1;
            }
        }
        else{
            foreach ($articles as $article) {

                foreach ($article->getTags() as $cat) {

                    if ($tag == $cat->getName()) {

                        $lists[$i] = $article;

                        $i = $i + 1;
                    }
                }
            }
        }

        $content = $this
            ->get('templating')
            ->render('AppBundle:Article:list.html.twig',
                array(
                    'Articles' => $lists
                ));

        return new Response($content);
    }

}