<?php namespace App\Backend\IpProtection\Classes;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use Nette\Object;
/**
 * IpAccess Class
 */
class IpAccess extends Object
{
	
	private $giveaccess = true;

	private $proxyList;

	private $blockedList;

	private $proxyHeaders = array(
		'HTTP_VIA',
		'HTTP_X_FORWARDED_FOR',
		'HTTP_FORWARDED_FOR',
		'HTTP_X_FORWARDED',
		'HTTP_FORWARDED',
		'HTTP_CLIENT_IP',
		'HTTP_FORWARDED_FOR_IP',
		'HTTP_X_SURFCACHE_FOR',
		'VIA',
		'X_FORWARDED_FOR',
		'FORWARDED_FOR',
		'X_FORWARDED',
		'FORWARDED',
		'CLIENT_IP',
		'FORWARDED_FOR_IP',
		'HTTP_PROXY_CONNECTION'
	);

	private $proxyPorts = array(3128, 6667, 8080);

	function __construct($blockedList, $proxyList)
	{
		$this->blockedList = $blockedList;
		$this->proxyList = $proxyList;
		$this->catchProxy();
	}

	public function proxyHadAccess()
	{
		return $this->giveaccess;
	}

	public function IsMyIpBlocked()
	{
		return in_array($_SERVER['REMOTE_ADDR'], $this->blockedList);
	}

	/**
	 * check if is a proxy client
	 * @return [type] [description]
	 */
	protected function catchProxy()
	{
		foreach($this->proxyHeaders as $header)
		{
			if(array_key_exists($header, $_SERVER))
			{
				$this->giveaccess = false;
			}
		}

		if($this->giveaccess)
		{
			foreach($this->proxyPorts as $port)
			{
				$sock = @fsockopen($_SERVER['REMOTE_ADDR'], $port, $errno, $errstr, 6);
 
				if ($sock)
				{
					$this->giveaccess = false;
						  break;
				}
			}

		}

		if(!$this->giveaccess)
		{
			$this->giveaccess = in_array($_SERVER['REMOTE_ADDR'], $this->proxyList);
		}
	}

}