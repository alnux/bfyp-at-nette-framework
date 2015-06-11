<?php namespace App\Backend\Acl\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use App\Backend\Presenters\BackendBasePresenter;


abstract class AclBasePresenter extends BackendBasePresenter{

    public function startup()
    {
        parent::startup();

        $this->template->activeacl = 'active';
    }

}