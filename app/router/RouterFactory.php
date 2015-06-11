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
		$router[] = new Route('/', 'Front:Index:default', Route::ONE_WAY);

		// -- Common module --------------------------------------------------------------------------------------------

		$router[] = new Route('user/profile/<action>/[/<id>]', array(
			'module' => 'Common',
			'presenter' => 'Profile',
			'action' => 'default',
			'id' => NULL
		));

		$router[] = new Route('sign/<action>/[/<id>]', array(
			'module' => 'Common',
			'presenter' => 'Sign',
			'action' => 'signin',
			'id' => NULL
		));

		// -- Backend module -------------------------------------------------------------------------------------------

		$router[] = new Route('admin/<presenter>/<action>/[/<id>]',array(
			'module' => 'Backend',
			'presenter' => 'Index',
			'action' => 'default',
			'id' => NULL
		));
		// -- Backend acl module ------------------------------------------------------------------------------------
		/*$router[] = new Route('admin/acl/<presenter>/<action>/[/<id>]', array(
			'module' => 'Backend:Acl',
			'presenter' => 'Index',
			'action' => 'index',
			'id' => NULL
		));*/

		// -- Front module  --------------------------------------------------------------------------------------------
		
		$router[] = new Route('user/<presenter>/<action>/[/<id>]', array(
			'module' => 'Front:Users',
			'presenter' => 'Index',
			'action' => 'default',
			'id' => NULL
		));

		$router[] = new Route('<presenter>/<action>/[/<id>]', array(
			'module' => 'Front',
			'presenter' => 'Index',
			'action' => 'default',
			'id' => NULL
		));

		return $router;
	}

}
