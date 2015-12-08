<?php

namespace Zethzer\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ZethzerUserBundle extends Bundle
{
	public function getParent(){
		return 'FOSUserBundle';
	}
}
