<?php namespace App\Backend\Acl\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */

class PrivilegePresenter extends AclBasePresenter
{
    public function startup()
    {
        parent::startup();
        $this->model = $this->model('Privilege');
        
        $this->template->activeaclprivilege = 'active';

        $this['breadCrumb']->addLink($this->translator->translate("Privileges"));
        
    }

    //-- Renders -------------------------------------------------------------------------------------------------------
    public function renderDefault()
    {
        $this->template->title = $this->translator->translate("Privileges");
        $this->template->nodes = $this->model;
        $this->template->parents = $this->model->getChildNodes();
    }

    public function renderNewEdit()
    {
        $this->template->title = $this->translator->translate("New Privilege");
        $this->template->page_description = $this->translator->translate("Create the new privilege and save it");
        $this['breadCrumb']->editLink($this->translator->translate("Privileges"),$this->link('Privilege:'));
        $this['breadCrumb']->addLink($this->template->title);
    }

    public function renderEdit()
    {
        $this->template->title = $this->translator->translate("Edit privilege");
        $this->template->page_description = $this->translator->translate("Please edit the privilege settings and save it");
        $this['breadCrumb']->editLink($this->translator->translate("Privileges"),$this->link('Privilege:'));
        $this['breadCrumb']->addLink($this->template->title);

        $this->setView('newedit');
    }

    // -- Actions ------------------------------------------------------------------------------------------------------

    public function actionNewEdit()
    {
        $this->checkAccess('create');
    }

    public function actionEdit($postId)
    {
        $this->checkAccess('edit');

        $privilege = $this->model->getOnArray($postId);
        if (!$privilege)
        {
            $this->flashMessage($this->translator->translate("Could not find the required privilege"), 'danger');
            $this->redirect('default');
        }
       
        $privilege["comment"]=$privilege["comment"]?:"";
        $this['newEdit']->setDefaults($privilege);
    }

    public function actionDelete($postId)
    {
        $this->checkAccess('delete');

        $privilege = $this->model->get($postId);
        if (!$privilege)
        {
            $this->flashMessage($this->translator->translate("Could not find the required privilege"), 'danger');
        }

        elseif($this->model->hasChildNodes($postId))
        {
            $this->flashMessage($this->translator->translate("The privilege can not be removed because it contains dependent privileges"), 'danger');
        }

        elseif($this->model->hasPermissionRelation($postId, $this->model->getRoleOrResourcefield()))
        {
            $this->flashMessage($this->translator->translate("The privilege can not be eliminated due to be present in permits"), 'danger');
        }

        else
        {
            $this->model->delete($postId);
            $this->flashMessage($this->translator->translate("The privilege has been successfully removed"), 'success');
        }
        $this->redirect('default');

    }

    //-- Components ----------------------------------------------------------------------------------------------------
    protected function createComponentNewEdit()
    {
        return $this->forms->getAclFactory($this)->privilegeForm();
    }

}