<?php namespace App\Common\Classes;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use \Nette\DI\Container;
use App\Common\Model as CM; // Common models
use App\Backend\Acl\Model as ACL; // Acl models
use App\Backend\IpProtection\Model as IPP; // Ip Protection models

/**
 * Container to find all app model
 */
class ModelsContainer {

    private $container;

    function __construct(Container $container)
    {
        $this->container = $container;
    }
    // -- Common Models ------------------------------------------------------------------------------------
        public function getUser()
    {
        return new CM\UserModel($this->container);
    }

    public function getLanguage()
    {
        return new CM\LanguageModel($this->container);        
    }
    // -- Acl ---------------------------------------------------------------------------------------------
    public function getPrivilege()
    {
        return new ACL\PrivilegeModel($this->container);
    }

    public function getResource()
    {
        return new ACL\ResourceModel($this->container);
    }

    public function getRole()
    {
        return new ACL\RoleModel($this->container);
    }

    public function getPermission()
    {
        return new ACL\PermissionModel($this->container);
    }

    public function getUserAcl()
    {
        return new ACL\UserAclModel($this->container);
    }

    public function getUserRole()
    {
        return new ACL\UserRoleModel($this->container);
    }

    public function getAclSP()
    {
        return new ACL\AclSecurityPermission($this->container);
    }

    // -- IPProtection -----------------------------------------------------------------------------------
    public function getWhiteProxy()
    {
        return new IPP\WhiteProxyModel($this->container);
    }

    public function getBlackIp()
    {
        return new IPP\BlackIpModel($this->container);
    }   




}
