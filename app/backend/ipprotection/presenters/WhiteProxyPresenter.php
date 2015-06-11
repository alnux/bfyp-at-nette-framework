<?php namespace App\Backend\IpProtection\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */

/**
 * ProxyWhite Presenter.
 */
class WhiteProxyPresenter extends IpProtectionBasePresenter
{
	//-- StartUp -----------------------------------------------------------

	public function startup()
	{
		parent::startup();
        $this->model = $this->model('WhiteProxy');
        
        $this->template->activeippwhiteproxy = 'active';
        $this['breadCrumb']->addLink($this->translator->translate("Proxies Accepted"));
	}
	//-----------------------------------------------------------------------


	//-- Renders -------------------------------------------------------------
	public function renderDefault()
	{
		$this->template->whiteproxys = $this->model->all();
		$this->template->title = $this->translator->translate("Proxies Accepted");
	}

	public function renderNewEdit()
	{
		$this['breadCrumb']->editLink($this->translator->translate("Proxies Accepted"),$this->link('WhiteProxy:'));
		$this['breadCrumb']->addLink($this->translator->translate("New proxy"));

		$this->template->title = $this->translator->translate("New proxy");
	}

	public function renderEdit()
	{
		$this['breadCrumb']->editLink($this->translator->translate("Proxies Accepted"),$this->link('WhiteProxy:'));
		$this['breadCrumb']->addLink($this->translator->translate("Edit proxy"));

		$this->template->title = $this->translator->translate("Edit proxy");
		$this->setView('newedit');
	}


	//------------------------------------------------------------------------

	//-- Actions -------------------------------------------------------------
	
	public function actionNewEdit()
	{
		$this->checkAccess('create');
	}

	/**
	 * Edit white proxy
	 * @param  integer $postId ID
	 * @return Void
	 */
	public function actionEdit($postId)
	{
		$this->checkAccess('edit');

		$whiteproxy = $this->model->getOnArray($postId);
		if (!$whiteproxy) 
		{
			$this->flashMessage($this->translator->translate("Could not find the required proxy"), 'danger');
			$this->redirect('default');
		}
		$whiteproxy["reason"]=$whiteproxy["reason"]?:"";
		$this['newEdit']->setDefaults($whiteproxy);
	}

		/**
	 * delete white proxy
	 * @param  integer $id ID
	 * @return redirect
	 */
	public function actionDelete($postId)
	{
		$this->checkAccess('delete');

		$blackip = $this->model->get($postId);
		if (!$blackip) 
		{
			$this->flashMessage($this->translator->translate("Could not find the required proxy"), 'danger');
		}

		else
		{
			$this->model->delete($postId);
			$this->deleteIpProtectionCache();
			$this->flashMessage($this->translator->translate("Proxy has been successfully removed"), 'success');
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