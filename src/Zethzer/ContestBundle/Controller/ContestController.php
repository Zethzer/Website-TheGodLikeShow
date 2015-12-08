<?php

namespace Zethzer\ContestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContestController extends Controller
{
    public function indexAction()
    {
        return $this->render('ZethzerContestBundle:Contest:index.html.twig');
    }
}
