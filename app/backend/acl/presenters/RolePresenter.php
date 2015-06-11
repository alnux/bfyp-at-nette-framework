<?php namespace App\Backend\Acl\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */

class RolePresenter extends AclBasePresenter
{
    public function startup()
    {
        parent::startup();
        $this->model = $this->model('Role');

        $this->template->activeaclrole = 'active';

        $this['breadCrumb']->addLink($this->translator->translate("Roles"));

    }

    // -- Renders ------------------------------------------------------------------------------------------------------
    public function renderDefault()
    {
        $this->template->title = $this->translator->translate("Roles");
        $this->template->nodes = $this->model;
        $this->template->parents = $this->model->getChildNodes();
    }

    public function renderNewEdit()
    {
        $this->template->title = $this->translator->translate("New Role");
        $this->template->page_description = $this->translator->translate("Create the new role and save it");
        $this['breadCrumb']->editLink($this->translator->translate("Roles"),$this->link('Role:'));
        $this['breadCrumb']->addLink($this->template->title);
    }

    public function renderEdit()
    {
        $this->template->title = $this->translator->translate("Edit role");
        $this->template->page_description = $this->translator->translate("Please edit the role settings and save it");
        $this['breadCrumb']->editLink($this->translator->translate("Roles"),$this->link('Role:'));
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

        $roleArray = $this->model->getOnArray($postId);
        if (!$roleArray)
        {
            $this->flashMessage($this->translator->translate("Could not find the role required"), 'danger');
            $this->redirect('default');
        }

        $roleArray["comment"]=$roleArray["comment"]?:"";
        $this['newEdit']->setDefaults($roleArray);
    }

    public function actionDelete($postId)
    {
        $this->checkAccess('delete');

        $role = $this->model->get($postId);
        if (!$role)
        {
            $this->flashMessage($this->translator->translate("Could not find the role required"), 'danger');
        }

        elseif($this->model->hasChildNodes($postId))
        {
            $this->flashMessage($this->translator->translate("The role can not be removed because it contains dependent roles"), 'danger');
        }

        elseif($this->model->hasPermissionRelation($postId, $this->model->getRoleOrResourcefield()))
        {
            $this->flashMessage($this->translator->translate("The role can not be removed due to be present in permits"), 'danger');
        }

        else
        {
            $this->model->delete($postId);
            $this->flashMessage($this->translator->translate("The role has been successfully removed"), 'success');
        }
        $this->redirect('default');

    }

    //-- Components ----------------------------------------------------------------------------------------------------
    protected function createComponentNewEdit()
    {
        return $this->forms->getAclFactory($this)->roleForm();
    }

}