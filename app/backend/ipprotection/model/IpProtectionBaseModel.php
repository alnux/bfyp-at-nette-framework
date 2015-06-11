<?php namespace App\Backend\IpProtection\Model;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use App\Common\Model\BaseModel;

class IpProtectionBaseModel extends BaseModel{

	public function ipsOnArray()
	{
		$ips = array();
		$rows = $this->all();
		foreach($rows as $row)
		{
			$ips[] = $row->ip;
		}
		return $ips;
	}

}