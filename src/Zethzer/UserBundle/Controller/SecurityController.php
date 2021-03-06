<?php

namespace Zethzer\UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseFOSSecurity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends BaseFOSSecurity
{
	protected function renderLogin(array $data)
	{
		// Sur la page du formulaire de connexion, on utilise la vue classique "login"
		// Cette vue hérite du layout et ne peut donc Ãªtre utilisée qu'individuellement
		if ($this->container->get('request')->attributes->get('_route') == 'fos_user_security_login') {
			$view = 'login';
		} else {
		// Mais sinon, il s'agit du formulaire de connexion intégré au menu, on utilise la vue "login_content"
		// car il ne faut pas hériter du layout !
			$view = 'login_content';
		}

		$template = sprintf('ZethzerUserBundle:Security:%s.html.%s', $view, $this->container->getParameter('fos_user.template.engine'));
		return $this->container->get('templating')->renderResponse($template, $data);
	}
}
