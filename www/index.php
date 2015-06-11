<?php

// Uncomment this line if you must temporarily take down your site for maintenance.
// require '.maintenance.php';
define('APP_DIR', __DIR__ . '/../app');
$container = require __DIR__ . '/../app/bootstrap.php';

//-- IpAccess -- proxy white list and ip blacklist detector -----------------------------
$cache = new Nette\Caching\Cache($container->getByType('Nette\Caching\IStorage'), $container->getParameters()['settings']['cachenamespace']);
$ipaccess = $cache->load('ipaccess');
if($ipaccess === NULL)
{
	$ipaccess = new App\Backend\IpProtection\Classes\IpAccess($container->getByType('App\Common\Classes\ModelsContainer')->getBlackIp()->ipsOnArray(), $container->getByType('App\Common\Classes\ModelsContainer')->getWhiteProxy()->ipsOnArray());
	$cache->save('ipaccess',$ipaccess, array(
		Nette\Caching\Cache::FILES => APP_DIR.'/config/config.neon'
		));
}
if($ipaccess->proxyHadAccess() == false)
	require '.maintenance.php';

if($ipaccess->IsMyIpBlocked() == true)
	require '.maintenance.php';
//---------------------------------------------------------------------------------------

$container->getByType('Nette\Application\Application')->run();
