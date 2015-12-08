<?php

namespace Zethzer\ForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ForumController extends Controller
{
    public function indexAction()
    {
        return $this->render('ZethzerForumBundle:Forum:index.html.twig');
    }
}