<?php namespace App\Backend\Acl\Classes;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */

use \App\Common\Classes\ModelsContainer,
	\App\Common\Classes\SettingsCaller;

/**
 * Permission access control list
 *
 */
class AclTreeMaker extends Permission {

	public function __construct(ModelsContainer $modeler, SettingsCaller $settings) {

		$model = $modeler->getAclSP();
		$roles = $model->getRoles();

		foreach($roles as $role)
		{
			$this->addRole($role['key_name'], $role['parent_key']);
		}

		$resources = $model->getResources();
		foreach($resources as $resource)
		{
			$this->addResource($resource['key_name'], $resource['parent_key']);
		}
		
		$rules = $model->getRules();
		foreach($rules as $rule)
		{
			if($rule->verification == sha1($rule['role_id'].$rule['privilege_id'].$rule['resource_id'].$settings->getParam('appkey').$rule['access']))
			{
				$this->{$rule->access ? 'allow' : 'deny'}($rule->role, $rule->resource, $rule->privilege);
			}
		}
		$this->addRole($settings->getParam('superuser'));
		$this->allow($settings->getParam('superuser'));
	}
}