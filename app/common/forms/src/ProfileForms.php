<?php namespace App\Common\Forms\Src;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */

use Nette\Application\UI\Form;
use App\Common\Forms\BaseForm;
/**
 * Form to edit pass
 */
class ProfileForms extends BaseForm
{
	public function changePass()
	{
		$this->form->addPassword('actual_password', 'Actual password')
			->setRequired('The actual password is required')
			->setAttribute('placeholder', 'Please insert the actual password')
			->addRule(Form::MAX_LENGTH, 'The max length of the password is %d',100)
			->addRule(Form::MIN_LENGTH, 'The min length of the password is %d', 8)
			->addRule(Form::PATTERN, 'You can use letters, numbers, and !@#$%^&* characters','[a-zA-Z0-9!@#$%^&*]+')
			->addRule(callback($this,'verifyActualPass'), 'Please insert the actual password');

		$this->form->addPassword('password_hash', 'New password')
			->setRequired('The new password is required')
			->setAttribute('placeholder', 'Please insert the new password using: letters, numbers and !@#$%^&* characters')
			->addRule(Form::MAX_LENGTH, 'The max length of the password is %d',100)
			->addRule(Form::MIN_LENGTH, 'The min length of the password is %d', 8)
			->addRule(Form::PATTERN, 'You can use letters, numbers, and !@#$%^&* characters','[a-zA-Z0-9!@#$%^&*]+');

		$this->form->addPassword('_password', 'Repeat new password')
			->setRequired('Please retype the password')
			->setAttribute('placeholder', 'Repeat the new password entered')
			->addRule(Form::EQUAL, 'Passwords must match', $this->form['password_hash']);

		$this->form->addSubmit('send', 'Change password');

		$this->form->onSuccess[] = callback($this, 'changePassProcess');
		return $this->form;
	}

	public function changePassProcess()
	{

		$values = $this->form->getValues();

		if($this->form->isValid())
		{
			unset($values['_password'], $values['actual_password']);
			$userId = $this->presenter->getVar('modeler')->getUser()->get($this->presenter->user->getIdentity()->getData()['id']);
			$values['password_hash'] = $this->presenter->getVar('modeler')->getUser()->generatePasswordHash($values['password_hash']);

			$userId->update($values);
			$message = $this->presenter->translator->translate('Your password has been updated');
		
			$this->presenter->flashMessage($message, 'success');
			$this->presenter->redirect('default');
		}
		else
		{
			$this->flashMessage($this->presenter->translator->translate('Could not save data'), 'danger');
			$this->presenter->redirect('default');
		}
	}

	//-- Custom rules ---------------------------------------------------------
	
	/**
	 * Verify actual password profile before change it
	 * @param  Form::addText $item  var catchs from form
	 * @return boolean
	 */
	public function verifyActualPass($item)
	{
		$userData = $this->presenter->user->getIdentity()->getData();
		return $this->presenter->getVar('modeler')->getUser()->verifyPasswordHash($item->value, $userData['password_hash']);
	}

	
}