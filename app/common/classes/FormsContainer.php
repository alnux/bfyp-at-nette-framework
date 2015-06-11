<?php namespace App\Common\Classes;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use App\Common\Forms\Src as CF; // Common forms container
use App\Backend\Acl\Forms\Src as ACL; // Acl Forms container
use App\Backend\IpProtection\Forms\Src as IPP; // IpProtection Forms container
/**
 * Container to find all forms
 */
class FormsContainer {

    public function getUserRegistry($presenter = NULL)
    {
        return new CF\UserRegistryForms($presenter);
    }

    public function getProfileFactory($presenter = NULL)
    {
        return new CF\ProfileForms($presenter);
    }

    public function getAclFactory($presenter = NULL)
    {
        return new ACL\AclForms($presenter);
    }

    public function getIppFactory($presenter = NULL)
    {
        return new IPP\IppForms($presenter);
    }

}