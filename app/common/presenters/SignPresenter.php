<?php namespace App\Common\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use \Nette\Security\Identity;

/**
 * Homepage presenter.
 */
class SignPresenter extends BasePresenter
{
	/** @inject @var \App\Common\Classes\EmailSender */
	public $emailer;

	/** @persistent */
    public $backlink = '';

	public function startup()
	{
		parent::startup();

		$this->bcrumbvar = ':Front:Index:default';

		$this->model = $this->model('User');
		$this->emailer->setTemplate($this->getTemplate());
		
	}
	// -- Renders --------------------------------------------------------------
	public function renderSignUp()
	{
		$this->template->title = $this->translator->translate("Sign Up");
		$this->template->page_description = $this->translator->translate("Please fill out the following fields to signup");
		$this->template->activesignup = 'active';
		$this['breadCrumb']->addLink($this->translator->translate('Sign Up'));
	}

	public function renderSignIn()
	{
		$this->template->title = $this->translator->translate("Sign In");
		$this->template->page_description = $this->translator->translate("Please fill out the following fields to login");
		$this->template->activesignin = 'active';
		$this['breadCrumb']->addLink($this->translator->translate('Sign In'));
	}

	public function renderRequestPassReset()
	{
		$this->template->title = $this->translator->translate('Request password reset');
		$this->template->page_description = $this->translator->translate("Please fill out your email. A link to reset password will be sent there");
		$this['breadCrumb']->addLink($this->translator->translate('Request password reset'));
	}

	public function renderResetPass()
	{
		$this->template->title = $this->translator->translate('Request password reset');
		$this->template->page_description = $this->translator->translate("Please fill the new password and repeat this");
		$this['breadCrumb']->addLink($this->translator->translate('Request password reset'));
	}


	// -- Actions --------------------------------------------------------------
	
	public function actionLogout()
	{
		$this->user->logout(TRUE);
		$this->redirect('signin');
	}

	public function actionValidate($hash)
	{
		if ($user = $this->model->findByValidationToken($hash))
		{
			$umodel = $this->model;
			$message = 'Your account is activate, now you can login';
			$type = 'success';
			$role = $this->model('Role')->customField($this->settingsValue('default_role'), 'key_name')->fetch();
			if(!is_object($role))
			{
				$message = 'Sorry but you can not be validated in the system. Please contact management';
				$type = 'danger';
			}
			else
			{
				$this->model('UserRole')->add(['user_id'=>$user->id, 'role_id'=>$role->id, 'verification'=>sha1($user->id.$this->settingsValue('appkey').$role->id)]);
				$user->update(['auth_key'=>'','status_value'=>$umodel::STATUS_ACTIVE]);
			}
		}
		else
		{
			$message = 'Sorry the validation code is invalid, please try again';
			$type = 'danger';
		}
		$this->flashMessage($this->translator->translate($message), $type);
		$this->redirect('signin');
	}


	// -- Components -----------------------------------------------------------
	protected function createComponentSignUp()
	{
		return $this->forms->getUserRegistry()->signUp();
	}

	protected function createComponentSignIn()
	{
		return $this->forms->getUserRegistry($this)->signIn();
	}

	protected function createComponentRequestPassReset()
	{
		return $this->forms->getUserRegistry()->requestPassReset();
	}

	protected function createComponentResetPass()
	{
		return $this->forms->getUserRegistry()->resetPass($this->getParameter('hash'));
	}

	//-- Other Methods -------------------------------------------------------------------------------------------------
	public function authenticate($username, $password, $rememberme)
	{
		$user['exist'] = false;
		if($user['user'] = $this->model->authUserOnDb($username, $password, $this->settingsValue('appkey')))
		{
			$user['exist'] = true;
			
			$umodel = $this->model;
			if($user['user']->status_value == $umodel::STATUS_PENDING)
			{
				$user['error'] = 'pending';
			}
			if($user['user']->status_value == $umodel::STATUS_BANNED)
			{
				$user['error'] = 'banned';
			}
			if($user['user']->status_value == $umodel::STATUS_PENDING || $user['user']->status_value == $umodel::STATUS_BANNED)
			{
				unset($user['user']);
			}

			if(isset($user['user']))
			{
				$this->user->login(new Identity($username, $this->model->getUserRoles(), $user['user']));

				if($rememberme)
				{
					$this->user->setExpiration(time()+7*24*60*60, FALSE);
				}
				else
				{
					$this->user->setExpiration(time()+30*60, TRUE, TRUE);
				}

			}

		}

		return $user;
	}

}
