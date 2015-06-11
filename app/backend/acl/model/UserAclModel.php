<?php namespace App\Backend\Acl\Model;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use App\Config\TablesSettings,
    App\Common\Model\UserModel;


class UserAclModel extends UserModel
{
    /**
     * Get user roles selected for UserForm class editing
     * @param  int $value user id
     * @return array rules selected by the user
     */
    public function userSelectedRules($value)
    {
        $result = array();
        $rows =$this->context->table(TablesSettings::T_USER_ROLE)->select('*')->where('user_id', $value);
        foreach ($rows as $row => $value)
        {
            $result[$value->role_id] = $value->role_id;
        }
        return $result;
    }

    /**
     * Insert relation between user and role
     *
     * @param array
     */
    public function insertUserRole($values)
    {
        $this->context->table(TablesSettings::T_USER_ROLE)->insert($values);
    }

    /**
     * Delete all user roles from T_USERS_ROLES table
     * @param  int $id user id
     */
    public function deleteUserRoles($id)
    {
        $this->context->table(TablesSettings::T_USER_ROLE)->where('user_id', $id)->delete();
    }

    /**
     * Get users roles on a string to users list
     * @param  int $idUser
     * @return string roles
     */
    public function infoUserRoles($idUser)
    {
        $roles = $this->context->table(TablesSettings::T_USER_ROLE)->where('user_id',$idUser);
        $string = '';
        foreach($roles as $role)
        {
            $string.= $role->rol->name.', ';
        }
        return substr($string, 0, -2);
    }
}