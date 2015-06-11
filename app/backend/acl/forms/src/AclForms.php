<?php namespace App\Backend\Acl\Forms\Src;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use Nette\Application\UI\Form;
use App\Common\Forms\BaseForm;

class AclForms extends BaseForm
{
    // -- User role form -----------------------------------------------------------------------------------------------
    public function editUserRoleForm()
    {
        $roles = $this->presenter->model('Role')->getTreeValues();
        unset($roles[0]);

        $this->form->addMultiSelect('role_id', 'Role', $roles, 15)
            ->setRequired('Please select a role');

        $this->form->addSubmit('send', 'Update role');

        $this->form->onSuccess[] = callback($this, 'editUserRoleProcess');
        return $this->form;
    }

    public function editUserRoleProcess()
    {

        $values = $this->form->getValues();

        if($this->form->isValid())
        {
            $roles = $values['role_id'];
            unset($values['_password'], $values['role_id']);

            if($this->presenter->getParameter('postId'))
            {
                $this->presenter->getVar('model')->deleteUserRoles($this->presenter->getParameter('postId'));
                $userId = $this->presenter->getVar('model')->get($this->presenter->getParameter('postId'));

                foreach ($roles as $role)
                {
                    $this->presenter->model('UserRole')->insertUserRole(array('user_id'=>$userId->id, 'role_id'=>$role, 'verification'=>sha1($userId->id.$this->presenter->settingsValue('appkey').$role)));
                }

                $this->presenter->flashMessage($this->presenter->translator->translate('User roles successfully upgraded'), 'success');
                $this->presenter->redirect('default');
            }
        }
        else
        {
            $this->flashMessage($this->presenter->translator->translate('Could not save data'), 'danger');
            $this->presenter->redirect('default');
        }
    }


    // -- Permission form ----------------------------------------------------------------------------------------------
    public function permissionForm()
    {
        // -- Roles
        $roles = $this->presenter->model('Role')->getTreeValues();
        unset($roles[0]);

        // -- Resources
        $resources = $this->presenter->model('Resource')->getTreeValues();
        $resources[0]= '- All resources -';

        // -- Privileges
        $prePrivileges = $this->presenter->model('Privilege')->all();
        $privileges[0] = '- All privileges -';
        foreach($prePrivileges as $privilege)
        {
            $privileges[$privilege->id] = $privilege->name;
        }

        // -- Access
        $access = array(1 => 'Access granted', 0 => 'Access denied');


        $this->form->addSelect('role_id', 'Role', $roles, 15)
            ->setRequired('Please select a role');

        $this->form->addSelect('resource_id', 'Resource', $resources, 15)
            ->setRequired('Please select a resource');

        $this->form->addSelect('privilege_id', 'Privilege', $privileges, 15)
            ->setRequired('Please select a privilege');

        $this->form->addRadioList('access', 'Access', $access)
            ->setRequired('Please select a type of access')
            ->addRule(Form::RANGE, 'The range should be between 0 and 1', array(0, 1));


        if(!$this->presenter->getParameter('postId'))
        {
            $this->form->addSubmit('send', 'Create permission');
        }
        else
        {
            $this->form->addSubmit('send', 'Edit permission');
        }
        $this->form->onSuccess[] = callback($this, 'permissionProcess');
        return $this->form;
    }

    public function permissionProcess()
    {
        $values = $this->form->getValues();

        $values['resource_id']=$values['resource_id']?:NULL;
        $values['privilege_id']=$values['privilege_id']?:NULL;

        if($this->form->isValid())
        {
            $values['verification'] = sha1($values['role_id'].$values['privilege_id'].$values['resource_id'].$this->presenter->settingsValue('appkey').$values['access']);
            if ($this->presenter->getParameter('postId'))
            {
                $post = $this->presenter->getVar('model')->get($this->presenter->getParameter('postId'));
                $post->update($values);
                $message = $this->presenter->translator->translate('Permission was updated');
            }
            else
            {
                $this->presenter->getVar('model')->add($values);
                $message = $this->presenter->translator->translate('Permission was created successfully');
            }

            $this->presenter->deleteAclCache();
            $this->presenter->flashMessage($message, 'success');
            $this->presenter->redirect('default');

        }
        else
        {
            $this->presenter->flashMessage($this->presenter->translator->translate('Could not save data'), 'danger');
            $this->presenter->redirect('default');
        }
    }

    // -- Role form ----------------------------------------------------------------------------------------------------
    public function roleForm()
    {
        $roles = $this->presenter->getVar('model')->getTreeValues($this->presenter->getParameter('postId'));

        $this->form->addText('key_name', 'Key')
            ->setRequired('The key field is required')
            ->setAttribute('placeholder', 'Insert the key with which the role is identified')
            ->addRule(Form::MAX_LENGTH, 'Max %d characters',64)
            ->addRule(Form::PATTERN, 'Only lowercase letters, characters - and _ are allowed','[a-z_-]+');

        $this->form->addText('name', 'Name')
            ->setRequired('The name field is required')
            ->setAttribute('placeholder', 'Insert the name for the role')
            ->addRule(Form::MAX_LENGTH, 'Max %d characters',64)
            ->addRule(Form::PATTERN, 'Only letters are allowed','[a-zA-ZñÑ ]+');

        $this->form->addSelect('parent_id', 'Parent rol', $roles, 15)
            ->setRequired('Select a parent role from the list');

        $this->form->addTextArea('comment', 'Comment')
            ->setAttribute('rows', 4)
            ->setAttribute('placeholder', 'Insert a comment for the role')
            ->addRule(Form::MAX_LENGTH, 'Max %d characters',250)
            ->addRule(Form::PATTERN, 'Only letters are allowed','[a-zA-ZñÑ ]*');

        if(!$this->presenter->getParameter('postId'))
        {
            $this->form['key_name']->addRule(callback($this,'unique'), 'This role code already exists','key_name');
            $this->form['name']->addRule(callback($this,'unique'), 'This role name already exists','name');
            $this->form->addSubmit('send', 'Create rol');
        }
        else
        {
            $this->form['key_name']->addRule(callback($this,'editExceptingSelf'), 'This role code already exists',array('field'=>'key_name','id'=>$this->presenter->getParameter('postId')));
            $this->form['name']->addRule(callback($this,'editExceptingSelf'), 'This role name already exists',array('field'=>'name','id'=>$this->presenter->getParameter('postId')));

            $this->form['parent_id']
                ->addRule(callback($this,'avoidSelfing'), 'You can not choose the same role as parent role',$this->presenter->getParameter('postId'))
                ->addRule(callback($this,'avoidChildAsParent'),'Please select an option that does not belong to the children of this role',$this->presenter->getParameter('postId'));

            $this->form->addSubmit('send', 'Edit rol');
        }

        $this->form->onSuccess[] = callback($this, 'roleProcess');
        return $this->form;
    }

    public function roleProcess()
    {
        $values = $this->form->getValues();

        if ($values['parent_id']==0)
        {
            $values['parent_id'] = NULL;
        }

        if($this->form->isValid())
        {
            if ($this->presenter->getParameter('postId'))
            {
                $post = $this->presenter->getVar('model')->get($this->presenter->getParameter('postId'));
                $post->update($values);
                $message = $this->presenter->translator->translate('The role was updated');
            }
            else
            {
                $this->presenter->getVar('model')->add($values);
                $message = $this->presenter->translator->translate('The role was created successfully');
            }

            $this->presenter->flashMessage($message, 'success');
            $this->presenter->redirect('default');
        }
        else
        {
            $this->presenter->flashMessage($this->presenter->translator->translate('Could not save data'), 'danger');
            $this->presenter->redirect('default');
        }
    }


    // -- Resource form ------------------------------------------------------------------------------------------------
    public function resourceForm()
    {
        $resources = $this->presenter->getVar('model')->getTreeValues($this->presenter->getParameter('postId'));

        $this->form->addText('key_name', 'Key')
            ->setRequired('The key field is required')
            ->setAttribute('placeholder', 'Insert the key with which the resource is identified')
            ->addRule(Form::MAX_LENGTH, 'Max %d characters', 64)
            ->addRule(Form::PATTERN, 'Only lowercase letters, numbers, characters - and _ are allowed', '[a-z0-9_-]+');

        $this->form->addText('name', 'Name')
            ->setRequired('The name field is required')
            ->setAttribute('placeholder', 'Insert the name for the resource')
            ->addRule(Form::MAX_LENGTH, 'Max %d characters', 64)
            ->addRule(Form::PATTERN, 'Only letters and numbers are allowed', '[a-zA-Z0-9ñÑ ]+');

        $this->form->addSelect('parent_id', 'Parent resource', $resources, 15)
            ->setRequired('Select a parent from resource list');

        $this->form->addTextArea('comment', 'Comment')
            ->setAttribute('rows', 4)
            ->setAttribute('placeholder', 'Insert a comment for the resource')
            ->addRule(Form::MAX_LENGTH, 'Max %d characters', 250)
            ->addRule(Form::PATTERN, 'Only letters and numbers are allowed', '[a-zA-Z0-9ñÑ _-]*');

        if (!$this->presenter->getParameter('postId')) {
            $this->form['key_name']->addRule(callback($this, 'unique'), 'This key resource already exists', 'key_name');
            $this->form['name']->addRule(callback($this, 'unique'), 'This name resource already exists', 'name');
            $this->form->addSubmit('send', 'Create resource');
        } else {
            $this->form['key_name']->addRule(callback($this, 'editExceptingSelf'), 'This key resource already exists', array('field' => 'key_name', 'id' => $this->presenter->getParameter('postId')));
            $this->form['name']->addRule(callback($this, 'editExceptingSelf'), 'This name resource already exists', array('field' => 'name', 'id' => $this->presenter->getParameter('postId')));
            $this->form['parent_id']
                ->addRule(callback($this, 'avoidSelfing'), 'You can not choose the same resource as the parent resource', $this->presenter->getParameter('postId'))
                ->addRule(callback($this, 'avoidChildAsParent'), 'Please select an option that does not belong to the children of this resource', $this->presenter->getParameter('postId'));

            $this->form->addSubmit('send', 'Edit resource');
        }
        $this->form->onSuccess[] = callback($this, 'resourceProcess');
        return $this->form;
    }

    public function resourceProcess()
    {
        $values = $this->form->getValues();

        if ($values['parent_id']==0)
        {
            $values['parent_id'] = NULL;
        }

        if($this->form->isValid())
        {
            if ($this->presenter->getParameter('postId'))
            {
                $post = $this->presenter->getVar('model')->get($this->presenter->getParameter('postId'));
                $post->update($values);
                $message = $this->presenter->translator->translate('The resource was updated successfully');
            }
            else
            {
                $this->presenter->getVar('model')->add($values);
                $message = $this->presenter->translator->translate('The resource was created successfully');
            }

            $this->presenter->deleteAclCache();
            $this->presenter->flashMessage($message, 'success');
            $this->presenter->redirect('default');
        }
        else
        {
            $this->presenter->flashMessage($this->presenter->translator->translate('Could not save data'), 'danger');
            $this->presenter->redirect('default');
        }
    }


    // -- Privilege form -------------------------------------------------------------------------------------------------
    public function privilegeForm()
    {
        $privileges = $this->presenter->getVar('model')->getTreeValues($this->presenter->getParameter('postId'));

        $this->form->addText('key_name', 'Key')
            ->setRequired('The key is required')
            ->setAttribute('placeholder', 'Insert a key to identify the privilege')
            ->addRule(Form::MAX_LENGTH, 'Max %d characters',64)
            ->addRule(Form::PATTERN, 'Only lowercase letters, characters - and _ are allowed','[a-z_-]+');

        $this->form->addText('name', 'Name')
            ->setRequired('The name is required')
            ->setAttribute('placeholder', 'Insert a name to privilege')
            ->addRule(Form::MAX_LENGTH, 'Max %d characters',64)
            ->addRule(Form::PATTERN, 'Only letters are allowed','[a-zA-ZñÑ ]+');

        $this->form->addSelect('parent_id', 'Parent privilege', $privileges, 15)
            ->setRequired('Select parent privilege');

        $this->form->addTextArea('comment', 'Comment')
            ->setAttribute('rows', 4)
            ->setAttribute('placeholder', 'Enter a comment for the privilege')
            ->addRule(Form::MAX_LENGTH, 'Max %d characters',250)
            ->addRule(Form::PATTERN, 'Only letters are allowed','[a-zA-ZñÑ ]*');

        if(!$this->presenter->getParameter('postId'))
        {
            $this->form['key_name']->addRule(callback($this,'unique'), 'This privilege code already exists','key_name');
            $this->form['name']->addRule(callback($this,'unique'), 'This privilege name already exists','name');
            $this->form->addSubmit('send', 'Create privilege');
        }
        else
        {
            $this->form['key_name']->addRule(callback($this,'editExceptingSelf'), 'This privilege code already exists',array('field'=>'key_name','id'=>$this->presenter->getParameter('postId')));
            $this->form['name']->addRule(callback($this,'editExceptingSelf'), 'This privilege name already exists',array('field'=>'name','id'=>$this->presenter->getParameter('postId')));
            $this->form['parent_id']
                ->addRule(callback($this,'avoidSelfing'), 'You can not choose the same privilege as the parent privilege',$this->presenter->getParameter('postId'))
                ->addRule(callback($this,'avoidChildAsParent'),'Please select an option that does not belong to the children of this privilege',$this->presenter->getParameter('postId'));
            $this->form->addSubmit('send', 'Edit privilege');
        }

        $this->form->onSuccess[] = callback($this, 'privilegeProcess');
        return $this->form;
    }

    public function privilegeProcess()
    {

        $values = $this->form->getValues();
        if ($values['parent_id']==0)
        {
            $values['parent_id'] = NULL;
        }

        if($this->form->isValid())
        {
            if($this->presenter->getParameter('postId'))
            {
                $post = $this->presenter->getVar('model')->get($this->presenter->getParameter('postId'));
                $post->update($values);
                $message = $this->presenter->translator->translate('The privilege was updated successfully');
            }
            else
            {
                $this->presenter->getVar('model')->add($values);
                $message = $this->presenter->translator->translate('The privilege was created successfully');
            }

            $this->presenter->flashMessage($message, 'success');
            $this->presenter->redirect('default');
        }
        else
        {
            $this->flashMessage($this->presenter->translator->translate('Could not save data'), 'danger');
            $this->presenter->redirect('default');
        }
    }
    // =================================================================================================================
    // -- Rules --------------------------------------------------------------------------------------------------------

    /**
     * Avoid child as parent
     * @param  Form::addText $item  var catchs from form
     * @param  int $id of child
     * @return boolean $response
     */
    public function avoidChildAsParent($item, $id)
    {
        $response = true;
        if($item->value != 0 || $item->value != NULL)
        {
            $response = !$this->presenter->getVar('model')->avoidChildAsParent($id, $item->value);
        }
        return $response;
    }

    /**
     * avoid select self id as parent
     * @param  object  $item     value selected for parent id
     * @param  integer $parentid actual id from
     * @return boolean identifies if selected the same item as the parent
     */
    public function avoidSelfing($item,$parentid=-1)
    {
        if($parentid!=-1)
        {
            if($item->value==$parentid)
            {
                return false;
            }
        }
        return true;
    }
}