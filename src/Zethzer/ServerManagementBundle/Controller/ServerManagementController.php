<?php

namespace Zethzer\ServerManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ServerManagementController extends Controller
{
    public function minecraftAction()
    {
        return $this->render('ZethzerServerManagementBundle:Server:minecraft.html.twig');
    }
}