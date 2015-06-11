<?php namespace App\Common\Classes;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use \Nette\Object;
/*
* Class to get parameters from settings parameter stage
*/
Class SettingsCaller extends Object
{
	protected $params;

	public function __construct($params)
	{
		$this->params = $params;
	}

	public function getParam($string)
	{
		$value ='';
		$this->getValue($value, explode('.', $string));
		return $value;
	}


   protected function getValue(&$value, $keys) 
	{
		$key = array_shift($keys);

		if($key != NULL)
		{
			$value = is_array($value)?$value[$key]:$this->params[$key];
			$this->getValue($value, $keys);
		}
	}

}