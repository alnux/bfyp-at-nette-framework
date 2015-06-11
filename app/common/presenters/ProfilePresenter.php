<?php namespace App\Common\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */

/**
 * Profile presenter.
 */
class ProfilePresenter extends BasePresenter
{

	//-- StartUp -----------------------------------------------------------
	public function startup()
	{
		parent::startup();
		$this->checkAccess();

		$this->bcrumbvar = ':Front:Users:Index:default';
		
		$this->template->menu = 'usersmenu';
		$this->template->activeuser = 'active';
		$this->template->activeuserprofile = 'active';
		
		$this['breadCrumb']->addLink($this->translator->translate("My profile"));
	}
	//----------------------------------------------------------------------
	
	//-- Renders -----------------------------------------------------------
	public function renderDefault()
	{
		$this->template->title = $this->translator->translate("My profile");
		$this->template->userdata['roles'] = implode(", ", $this->user->getRoles());
		$this->template->userdata['language'] = $this->model('Language')->get($this->user->getStorage()->getIdentity()->__get('language_key'))->name;
	}

	public function renderChangePass()
	{
		$this->template->title = $this->translator->translate("Change password");
		
		$this['breadCrumb']->editLink($this->translator->translate("My profile"),$this->link('Profile:'));
		$this['breadCrumb']->addLink($this->translator->translate("Change password"));

		$this->setView('changepassinfo');
	}

	//----------------------------------------------------------------------
	

	//-- Components --------------------------------------------------------
	
	protected function createComponentChangePass()
	{		
		return $this->forms->getProfileFactory($this)->changePass();
	}

	//----------------------------------------------------------------------
}