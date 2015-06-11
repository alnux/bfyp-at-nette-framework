<?php namespace App\Backend\Acl\Model;

/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 * thanks to Tomas Marcanik's AclModel class
 */

use \App\Config\TablesSettings;

/**
 * Build Access control list for the users.
 */
class AclSecurityPermission extends AclBaseModel {

	/**
	 * Put into array parents, used for roles and resources
	 * 
	 * @param integer ID of parent role
	 * @param string Key name of parent role
	 */
	public function getParent($parent_id, $parent_key, &$list, $table)
	{
		$rows = $this->context->table($table)->where('parent_id', $parent_id);
		foreach ($rows as $row)
		{
			$list[] = array('key_name' => $row->key_name, 'parent_key' => $parent_key);
			$this->getParent($row->id, $row->key_name, $list, $table);
		}
	}

	/**
	 * Return all roles hierarchically ordered
	 * 
	 * @return  array
	 */
	public function getRoles() {
		$list = array();
		$this->getParent(NULL, NULL, $list, TablesSettings::T_ROLE);
		return $list;
	}

	/**
	 * Return all resources hierarchically ordered
	 *
	 * @return  array
	 */
	public function getResources()
	{
		$list = array();
		$this->getParent(NULL, NULL, $list, TablesSettings::T_RESOURCE);
		return $list;
	}

	/**
	 * Return all rules of permissions
	 *
	 * @return  object
	 */
	public function getRules() {
		$rows = $this->context->fetchAll('
		SELECT
			a.role_id as role_id,
			a.privilege_id as privilege_id,
			a.resource_id as resource_id,
			a.access as access,
			a.verification as verification,
			ro.key_name as role,
			re.key_name as resource,
			p.key_name as privilege
			FROM '.TablesSettings::T_PERMISSION.' a
			JOIN '.TablesSettings::T_ROLE.' ro ON (a.role_id = ro.id)
			LEFT JOIN '.TablesSettings::T_RESOURCE.' re ON (a.resource_id = re.id)
			LEFT JOIN '.TablesSettings::T_PRIVILEGE.' p ON (a.privilege_id = p.id)
			ORDER BY a.id ASC
		');
		return $rows;
	 }

}