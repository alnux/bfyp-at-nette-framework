<?php namespace App\Backend\Acl\Model;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use App\Config\TablesSettings;


/**
 * Privileges Model Class.
 */
class PrivilegeModel extends AclBaseModel {
 
	protected $table = TablesSettings::T_PRIVILEGE;
	protected $roleResourceField = 'privilege_id';

	/**
	 * @return $roleResourceField return this variable
	 */
	public function getRoleOrResourceField()
	{
		return $this->roleResourceField;
	}

}