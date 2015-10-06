<?php

namespace Teddy\Router;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\SimpleRouter;



class RouterFactory
{

	/**
	 * @return \Nette\Application\IRouter
	 */
	public static function create()
	{
		$router = new RouteList();

		$router[] = new Route('', 'Index:Homepage:default');

		$router[] = new Route('zapomenute-heslo/nastavit-nove/<token>', array(
			'module' => 'Index',
			'presenter' => 'ForgottenPassword',
			'action' => 'setNew',
			'token' => NULL,
		));
		$router[] = new Route('zapomenute-heslo/<action>/<token>', array(
			'module' => 'Index',
			'presenter' => 'ForgottenPassword',
			'action' => 'default',
			'token' => NULL,
		));

		$router[] = new Route('cron/<action>/<id>', array(
			'module' => 'Cron',
			'presenter' => 'Cron',
			'action' => 'default',
			'id' => NULL,
		));

		$router[] = new Route('admin/<presenter>/<action>/<id>', array(
			'module' => 'Admin',
			'presenter' => 'Main',
			'action' => 'default',
			'id' => NULL,
		));

		$router[] = new Route('<presenter>/<action>/<id>', array(
			'module' => 'Game',
			'presenter' => 'Default',
			'action' => 'default',
			'id' => NULL,
		));
		return $router;
	}

}
