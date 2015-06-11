<?php namespace App\Front\Users\Example\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use App\Front\Users\Presenters\UsersBasePresenter;

/**
 * Example sub sub module presenter.
 */
class ExamplePresenter extends UsersBasePresenter
{
	//-- Renders -------------------------------------------------------------------------------------------------------
    public function renderDefault()
    {
        $this->template->title = $this->translator->translate("Home");
    }
}
