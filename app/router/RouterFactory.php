<?php

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\SimpleRouter;


/**
 * Router factory.
 */
class RouterFactory
{

	/**
	 * @return \Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList();

        $router[] = new Route('cron/<action>/<id>', array(
            'module' => 'Cron',
            'presenter' => 'Cron',
            'action' => 'default',
            'id' => NULL,
        ));

        $router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
//
//        $router[] = new Route('<presenter>/<action>/<id>', array(
//            'module' => 'Game',
//            'presenter' => 'Default',
//            'action' => 'default',
//            'id' => NULL,
//        ));
        return $router;
	}

}
