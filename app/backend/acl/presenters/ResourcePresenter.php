<?php namespace App\Backend\Acl\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */

class ResourcePresenter extends AclBasePresenter
{
    public function startup()
    {
        parent::startup();
        $this->model = $this->model('Resource');
        
        $this->template->activeaclresource = 'active';

        $this['breadCrumb']->addLink($this->translator->translate("Resources"));

    }

    // -- Renders -------------------------------------------------------------------------------------------------------
    public function renderDefault()
    {
        $this->template->title = $this->translator->translate("Resources");
        $this->template->nodes = $this->model;
        $this->template->parents = $this->model->getChildNodes();
    }

    public function renderNewEdit()
    {
        $this->template->title = $this->translator->translate("New resource");
        $this->template->page_description = $this->translator->translate("Create the new resource and save it");
        $this['breadCrumb']->editLink($this->translator->translate("Resources"),$this->link('Resource:'));
        $this['breadCrumb']->addLink($this->template->title);
    }

    public function renderEdit()
    {
        $this->template->title = $this->translator->translate("Edit resource");
        $this->template->page_description = $this->translator->translate("Please edit the resource settings and save it");
        $this['breadCrumb']->editLink($this->translator->translate("Resources"),$this->link('Resource:'));
        $this['breadCrumb']->addLink($this->template->title);

        $this->setView('newedit');
    }

    // -- actions ------------------------------------------------------------------------------------------------------
    public function actionNewEdit()
    {
        $this->checkAccess('create');
    }

    public function actionEdit($postId)
    {
        $this->checkAccess('edit');

        $resourceArray = $this->model->getOnArray($postId);
        if (!$resourceArray)
        {
            $this->flashMessage($this->translator->translate("Could not find the resource required"), 'danger');
            $this->redirect('default');
        }
        $resourceArray["comment"]=$resourceArray["comment"]?:"";
        $this['newEdit']->setDefaults($resourceArray);

    }

    public function actionDelete($postId)
    {
        $this->checkAccess('delete');

        $resource = $this->model->get($postId);
        if (!$resource)
        {
            $this->flashMessage($this->translator->translate("Could not find the resource required"), 'danger');

        }

        elseif($this->model->hasChildNodes($postId))
        {
            $this->flashMessage($this->translator->translate("The resource can not be removed because it contains dependent resources"), 'danger');
        }

        elseif($this->model->hasPermissionRelation($postId, $this->model->getRoleOrResourcefield()))
        {
            $this->flashMessage($this->translator->translate("The resource can not be removed due to be present in permits"), 'danger');
        }

        else
        {
            $this->model->delete($postId);
            $this->deleteAclCache();
            $this->flashMessage($this->translator->translate("The resource has been successfully removed"), 'success');
        }
        $this->redirect('default');
    }

    //-- Components ----------------------------------------------------------------------------------------------------
    protected function createComponentNewEdit()
    {
        return $this->forms->getAclFactory($this)->resourceForm();
    }

}