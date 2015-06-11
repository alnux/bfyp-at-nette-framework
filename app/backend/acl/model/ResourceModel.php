<?php namespace App\Backend\Acl\Model;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use App\Config\TablesSettings;


/**
 * Privileges Model Class.
 */
class ResourceModel extends AclBaseModel {
 
	protected $table = TablesSettings::T_RESOURCE;
	protected $roleResourceField = 'resource_id';

	/**
	 * @return $roleResourceField return this variable
	 */
	public function getRoleOrResourcefield()
	{
		return $this->roleResourceField;
	}

}