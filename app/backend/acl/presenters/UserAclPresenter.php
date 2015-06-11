<?php namespace App\Backend\Acl\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */

class UserAclPresenter extends AclBasePresenter{

    public function startup()
    {
        parent::startup();
        $this->model = $this->model('UserAcl');
        
        $this->template->activeacluser = 'active';

        $this['breadCrumb']->addLink($this->translator->translate("Users"));

    }

    // -- Renders -------------------------------------------------------------------------------------------------------
    public function renderDefault()
    {
        $this->template->users = $this->model->all();
        $this->template->roles = $this->model;
        $this->template->title = $this->translator->translate("Users");
    }

    public function renderEditRole()
    {
        $this->template->title = $this->translator->translate("Edit role");
        $this->template->page_description = $this->translator->translate("Please edit the user role and save it");
        $this['breadCrumb']->editLink($this->translator->translate("Users"),$this->link('UserAcl:'));
        $this['breadCrumb']->addLink($this->template->title);

    }

    // -- Actions ------------------------------------------------------------------------------------------------------

    public function actionEditRole($postId)
    {
        $this->checkAccess('edit');
        $user = $this->model->getOnArray($postId);
        if (!$user)
        {
            $this->flashMessage($this->translator->translate("Could not find the user required"), 'danger');
            $this->redirect('default');
        }
        $this->template->userrole = $user;
        $user['role_id'] =  $this->model->userSelectedRules($postId);
        $this['editRole']->setDefaults($user);
    }

    //-- Components ----------------------------------------------------------------------------------------------------
    protected function createComponentEditRole()
    {
        return $this->forms->getAclFactory($this)->editUserRoleForm();
    }
    //------------------------------------------------------------------------------------------------------------------

}