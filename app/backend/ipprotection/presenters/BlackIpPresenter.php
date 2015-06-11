<?php namespace App\Backend\IpProtection\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */


/**
 * BlackIp Presenter.
 */
class BlackIpPresenter extends IpProtectionBasePresenter
{
	//-- StartUp -----------------------------------------------------------

	public function startup()
	{
		parent::startup();
        $this->model = $this->model('BlackIp');
        
        $this->template->activeippblackip = 'active';
        $this['breadCrumb']->addLink($this->translator->translate("Blocked Ips"));
	}
	//-----------------------------------------------------------------------

	//-- Renders -------------------------------------------------------------
	public function renderDefault()
	{
		$this->template->blackips = $this->model->all();
		$this->template->title = $this->translator->translate("Blocked Ips");
	}

	public function renderNewEdit()
	{
		$this['breadCrumb']->editLink($this->translator->translate("Blocked Ips"),$this->link('BlackIp:'));
		$this['breadCrumb']->addLink($this->translator->translate("Block new ip"));

		$this->template->title = $this->translator->translate("Block new ip");
	}

	public function renderEdit()
	{
		$this['breadCrumb']->editLink($this->translator->translate("Blocked Ips"),$this->link('BlackIp:'));
		$this['breadCrumb']->addLink($this->translator->translate("Edit Blocked ip"));

		$this->template->title = $this->translator->translate("Edit Blocked ip");
		$this->setView('newedit');
	}

	//------------------------------------------------------------------------

	//-- Actions -------------------------------------------------------------
	
	public function actionNewEdit()
	{
		$this->checkAccess('create');
	}

	/**
	 * Edit Ip blocked
	 * @param  integer $postId ID
	 * @return Void
	 */
	public function actionEdit($postId)
	{
		$this->checkAccess('edit');

		$blackip = $this->model->getOnArray($postId);
		if (!$blackip) 
		{
			$this->flashMessage($this->translator->translate("Could not find the required blocked ip"), 'danger');
			$this->redirect('default');
		}
		$blackip["reason"]=$blackip["reason"]?:"";
		$this['newEdit']->setDefaults($blackip);
	}

	/**
	 * delete Black ip
	 * @param  integer $id ID
	 * @return redirect
	 */
	public function actionDelete($postId)
	{
		$this->checkAccess('delete');

		$blackip = $this->model->get($postId);
		if (!$blackip) 
		{
			$this->flashMessage($this->translator->translate("Could not find the required blocked ip"), 'danger');
		}

		else
		{
			$this->model->delete($postId);
			$this->deleteIpProtectionCache();
			$this->flashMessage($this->translator->translate("Blocking the ip successfully removed"), 'success');
		}
		$this->redirect('default');

	}
	//------------------------------------------------------------------------

	//-- Components ----------------------------------------------------------
	protected function createComponentNewEdit()
    {
        return $this->forms->getIppFactory($this)->WhiteAndBlackForm();
    }
	//------------------------------------------------------------------------
}