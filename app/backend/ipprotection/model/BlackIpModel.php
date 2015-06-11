<?php namespace App\Backend\IpProtection\Model;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use App\Config\TablesSettings;
 
/**
 * BlackIp Model Class.
 */
class BlackIpModel extends IpProtectionBaseModel {
 
	protected $table = TablesSettings::T_BLACKIP;
	
}