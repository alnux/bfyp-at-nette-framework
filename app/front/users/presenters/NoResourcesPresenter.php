<?php namespace App\Front\Users\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use App\Front\Presenters\FrontBasePresenter;


class NoResourcesPresenter extends FrontBasePresenter{

	//-- Renders -----------------------------------------------------------------------------------------
    public function renderDefault()
    {
        $this->template->title = $this->translator->translate("No resources");
    }

}