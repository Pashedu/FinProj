<?php

namespace Softgroup\FinalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/about", name="softgroup_final")
     */
    public function indexAction()
    {
        return $this->render('SoftgroupFinalBundle:Default:index.html.twig');
    }
}
