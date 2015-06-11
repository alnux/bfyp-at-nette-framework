<?php namespace App\Backend\Acl\Model;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use App\Config\TablesSettings;


class PermissionModel  extends AclBaseModel
{
    protected $table = TablesSettings::T_PERMISSION;


    public function getPermissionsGrouped()
    {
        return $this->all()->group($this->table.'.role_id');
    }

    public function getResourcesGrouped($id)
    {
        return $this->customField($id, 'role_id')->group('resource_id');
    }

    public function getPrivileges($idRol,$idResource)
    {
        return $this->context->table($this->table)->where('role_id ? AND resource_id ?', $idRol, $idResource);

    }
}