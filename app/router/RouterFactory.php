<?php

namespace Teddy\Router;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\SimpleRouter;



class RouterFactory
{

	/**
	 * @warning: annotation is needed!
	 *
	 * @return \Nette\Application\IRouter
	 */
	public static function create()
	{
		$router = new RouteList();

		$router[] = new Route('', 'Index:Homepage:default');

		$router[] = new Route('zapomenute-heslo/nastavit-nove/<token>', [
			'module' => 'Index',
			'presenter' => 'ForgottenPassword',
			'action' => 'setNew',
			'token' => NULL,
		]);
		$router[] = new Route('zapomenute-heslo/<action>/<token>', [
			'module' => 'Index',
			'presenter' => 'ForgottenPassword',
			'action' => 'default',
			'token' => NULL,
		]);

		$router[] = new Route('admin/<presenter>/<action>/<id>', [
			'module' => 'Admin',
			'presenter' => 'Main',
			'action' => 'default',
			'id' => NULL,
		]);

		$router[] = new Route('<presenter>/<action>/<id>', [
			'module' => 'Game',
			'presenter' => 'Default',
			'action' => 'default',
			'id' => NULL,
		]);
		return $router;
	}

}
