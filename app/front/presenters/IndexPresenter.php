<?php namespace App\Front\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015 
 */
use Nette;

class IndexPresenter extends FrontBasePresenter{

	//-- Renders -------------------------------------------------------------------------------------------------------
    public function renderDefault()
    {
        $this->template->title = $this->translator->translate("Home");
    }

}
