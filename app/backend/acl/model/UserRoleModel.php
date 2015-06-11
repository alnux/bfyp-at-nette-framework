<?php namespace App\Backend\Acl\Model;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use App\Config\TablesSettings;


class UserRoleModel extends UserAclModel{

    protected $table = TablesSettings::T_USER_ROLE;



}