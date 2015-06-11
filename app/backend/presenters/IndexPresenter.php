<?php namespace App\Backend\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use Nette;

class IndexPresenter extends BackendBasePresenter{

	//-- Renders -------------------------------------------------------------------------------------------------------
    public function renderDefault()
    {
        $this->template->title = $this->translator->translate("Home");
    }

}