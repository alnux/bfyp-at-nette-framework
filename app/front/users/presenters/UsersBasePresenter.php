<?php namespace App\Front\Users\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use App\Front\Presenters\FrontBasePresenter;


abstract class UsersBasePresenter extends FrontBasePresenter{

	public function startup()
    {
        parent::startup();
        $this->checkAccess();
        
        $this->bcrumbvar = ':Front:Users:Index:default';

        $this->template->menu = 'usersmenu';
    }

}