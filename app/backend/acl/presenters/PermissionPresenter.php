<?php namespace App\Backend\Acl\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */

class PermissionPresenter extends AclBasePresenter
{
    public function startup()
    {
        parent::startup();
        $this->model = $this->model('Permission');
        
        $this->template->activeaclpermission = 'active';

        $this['breadCrumb']->addLink($this->translator->translate("Permissions"));

    }

    // -- Renders ------------------------------------------------------------------------------------------------------
    public function renderDefault()
    {
        $this->template->permissions = $this->model;
        $this->template->title = $this->translator->translate("Permissions");
    }

    public function renderNewEdit()
    {
        $this->template->title = $this->translator->translate("New Permission");
        $this->template->page_description = $this->translator->translate("Create the new permission and save it");
        $this['breadCrumb']->editLink($this->translator->translate("Permissions"),$this->link('Permission:'));
        $this['breadCrumb']->addLink($this->template->title);
    }

    public function renderEdit()
    {
        $this->template->title = $this->translator->translate("Edit permission");
        $this->template->page_description = $this->translator->translate("Please edit the permission settings and save it");
        $this['breadCrumb']->editLink($this->translator->translate("Permissions"),$this->link('Permission:'));
        $this['breadCrumb']->addLink($this->template->title);

        $this->setView('newedit');
    }

    //-- Actions -------------------------------------------------------------------------------------------------------

    public function actionNewEdit()
    {
        $this->checkAccess('create');
    }

    public function actionEdit($postId)
    {
        $this->checkAccess('edit');

        $permissionArray = $this->model->getOnArray($postId);

        if (!$permissionArray)
        {
            $this->flashMessage($this->translator->translate("Could not find the required permission"), 'danger');
            $this->redirect('default');
        }
        $this['newEdit']->setDefaults($permissionArray);

    }

    public function actionDelete($postId)
    {
        $this->checkAccess('delete');

        $permission = $this->model->get($postId);

        if (!$permission)
        {
            $this->flashMessage($this->translator->translate("Could not find the required permission"), 'danger');
            $this->redirect('default');
        }

        $this->model->delete($postId);
        $this->deleteAclCache();
        $this->flashMessage($this->translator->translate("Permission has been successfully removed"), 'success');
        $this->redirect('default');

    }


    //-- Components ----------------------------------------------------------------------------------------------------
    protected function createComponentNewEdit()
    {
        return $this->forms->getAclFactory($this)->permissionForm();
    }

}