<?php namespace App\Backend\IpProtection\Model;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use App\Config\TablesSettings;
 
/**
 * WhiteProxy Model Class.
 */
class WhiteProxyModel extends IpProtectionBaseModel {
 
	protected $table = TablesSettings::T_WHITEPROXY;

}