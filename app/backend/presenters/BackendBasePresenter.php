<?php namespace App\Backend\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use App\Common\Presenters\BasePresenter;


abstract class BackendBasePresenter extends BasePresenter{

	public function startup()
    {
        parent::startup();
        $this->bcrumbvar = ':Backend:Index:default';

        $this->checkAccess();

        $this->template->accesscreate = $this->checkAccess('create', false); 
        $this->template->accessedit = $this->checkAccess('edit', false); 
        $this->template->accessdelete = $this->checkAccess('delete', false);

        $this->template->menu = 'backendmenu';
    }

}