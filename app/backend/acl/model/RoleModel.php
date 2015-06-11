<?php namespace App\Backend\Acl\Model;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use App\Config\TablesSettings;


class RoleModel extends AclBaseModel
{

    protected $table = TablesSettings::T_ROLE;
    protected $roleResourceField = 'role_id';

    /**
     * @return $roleResourceField return this variable
     */
    public function getRoleOrResourceField()
    {
        return $this->roleResourceField;
    }

}