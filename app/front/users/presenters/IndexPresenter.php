<?php namespace App\Front\Users\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */

class IndexPresenter extends UsersBasePresenter
{
	//-- Renders -------------------------------------------------------------------------------------------------------
    public function renderDefault()
    {
        $this->template->title = $this->translator->translate("Home");
    }
}
