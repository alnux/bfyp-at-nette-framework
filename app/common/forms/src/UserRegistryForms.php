<?php namespace App\Common\Forms\Src;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use \Nette\Application\UI\Form;
use App\Common\Forms\BaseForm;

class UserRegistryForms extends BaseForm
{
    // -- Sign up form -------------------------------------------------------------------------------------------------
    public function signUp()
    {
        $this->form->addText('username', 'Username')
            ->setRequired('Username field is required')
            ->addRule(Form::MAX_LENGTH, 'The max length allowed is %d', 10)
            ->addRule(Form::PATTERN, 'Use only lowercase letters and numbers', '[a-z0-9]+')
            ->addRule(callback($this, 'isValueTaken'), 'The username %value already exists', 'username');

        $this->form->addText('email', 'Email')
            ->setRequired('Email field is required')
            ->addRule(Form::MAX_LENGTH, 'The max length allowed is %d', 200)
            ->addRule(Form::EMAIL, 'Please write a valid email address')
            ->addRule(callback($this, 'isValueTaken'), 'The email %value already exists', 'email');

        $this->form->addPassword('password', 'Password')
            ->setRequired('Password field is required')
            ->addRule(Form::MIN_LENGTH, 'the min length allowed is %d', 8)
            ->addRule(Form::MAX_LENGTH, 'The max length allowed is %d', 200)
            ->addRule(Form::PATTERN, 'the next characters are allowed @#%!*', '[a-zA-Z0-9@#%!*]+');

        $this->form->addPassword('_password', 'Repeat password')
            ->setRequired('Password match is required')
            ->addRule(Form::EQUAL, 'Passwords must match', $this->form['password']);


        $this->form->addSubmit('send', 'Sign Up');

        $this->form->onSuccess[] = callback($this, 'signUpProcess');
        return $this->form;
    }

    public function signUpProcess()
    {
        $presenter = $this->form->getPresenter();

        if ($this->form->isValid()) {
            $values = $this->form->getValues();
            $new_sign_up = $presenter->getVar('model');
            $values['created_at'] = $new_sign_up->timestamp();
            $values['updated_at'] = $new_sign_up->timestamp();
            $values['password_hash'] = $new_sign_up->generatePasswordHash($values['password']);
            $values['auth_key'] = $new_sign_up->generateRandomString();
            $values['status_value'] = $new_sign_up::STATUS_PENDING;
            $values['user_type_value'] = $new_sign_up::TYPE_STATE;
            $values['language_key'] = $presenter->getBrowser('language');
            unset($values['_password'], $values['password']);

            if ($presenter->getVar('model')->add($values)) {

                $presenter->emailer->setParams((array)$values, 'updated_at password_hash created_at status_value user_type_value');

                $presenter->emailer->quickSend($values['email'], 'signin.user_registry', $presenter->getBrowser('language'));

                $presenter->flashMessage($presenter->translator->translate('You are registered, please check your email to validate'), 'success');
            } else {
                $presenter->flashMessage($presenter->translator->translate('Sorry but your account has not been created, please try again'), 'danger');
            }
        }
        $presenter->redirect('signup');
    }

    // -- Sign in form -------------------------------------------------------------------------------------------------
    public function signIn()
    {
        $this->form->addText('username', 'Username')
            ->setRequired('Username field is required');


        $this->form->addPassword('password_hash', 'Password')
            ->setRequired('Password field is required');

        $this->form->addCheckbox('rememberme', 'Remember Me');

        $this->form->addSubmit('send', 'Sign In');

        $this->form->onSuccess[] = callback($this, 'signInProcess');
        return $this->form;
    }

    public function signInProcess()
    {
        $values = $this->form->getValues();

        if($this->form->isValid())
        {
            $user = $this->presenter->authenticate($values['username'],$values['password_hash'],$values['rememberme']);

            if($user['exist'])
            {
                if(isset($user['error']))
                {
                    switch($user['error'])
                    {
                        case 'pending':
                            $message = 'You have to validate your account before to sign in';
                            break;
                        case 'banned':
                            $message = 'You are banned';
                            break;
                    }
                    $this->presenter->flashMessage($this->presenter->translator->translate($message), 'danger');
                    $this->presenter->redirect('signin');  
                }

                else
                {
                    $this->presenter->restoreRequest($this->presenter->backlink);
                    $this->presenter->redirect(':Front:Users:Index:default');
                }
            }
            else
            {
                $this->presenter->flashMessage($this->presenter->translator->translate('Please enter your username and/or password correctly'), 'danger');
                $this->presenter->redirect('signin');
            }

        }
        else
        {
            $this->presenter->flashMessage($this->presenter->translator->translate('Unable to perform action'), 'danger');
            $this->presenter->redirect(':Front:Users:Index:default');
        }
    }

    // -- Request password reset form ----------------------------------------------------------------------------------
    public function requestPassReset()
    {
        $this->form->addText('email', 'Email')
            ->setRequired('Email field is required')
            ->addRule(Form::EMAIL, 'Please write a valid email address')
            ->addRule(callback($this, 'checkEmailExists'), 'Sorry, the email %value does not exists on our registry', 'email');

        $this->form->addSubmit('send', 'Reset Password');

        $this->form->onSuccess[] = callback($this, 'requestPassResetProcess');
        return $this->form;
    }

    public function requestPassResetProcess()
    {
        $presenter = $this->form->getPresenter();
        if ($this->form->isValid()) {
            $values = $this->form->getValues();
            $user = $presenter->getVar('model')->UserByEmail($values['email']);
            $values['password_reset_token'] = $presenter->getVar('model')->generatePasswordResetToken();

            $user->update(['password_reset_token' => $values['password_reset_token']]);

            $values['username'] = $user->username;
            $presenter->emailer->setParams((array)$values);
            $presenter->emailer->quickSend($values['email'], 'requestpasswordreset.user_registry', $presenter->getBrowser('language'));
            $presenter->flashMessage($presenter->translator->translate('We send a reset password token to your email'), 'success');

            $presenter->redirect('signin');
        }
    }

    // -- Reset password )----------------------------------------------------------------------------------------------
    public function resetPass($hash)
    {
        $this->form->addPassword('password', 'Password')
            ->setRequired('Password field is required')
            ->addRule(Form::MIN_LENGTH, 'the min length allowed is %d', 8)
            ->addRule(Form::MAX_LENGTH, 'The max length allowed is %d', 200)
            ->addRule(Form::PATTERN, 'the next characters are allowed @#%!*', '[a-zA-Z0-9@#%!*]+');

        $this->form->addPassword('_password', 'Repeat password')
            ->setRequired('Password match is required')
            ->addRule(Form::EQUAL, 'Passwords must match', $this->form['password']);

        $this->form->addHidden('hash')
            ->setDefaultValue($hash)
            ->addRule(callback($this, 'isPasswordResetTokenValid'), 'Sorry the validation code is invalid or is expired, please re sent the code to your email');

        $this->form->addSubmit('send', 'Reset Password');

        $this->form->onSuccess[] = callback($this, 'resetPassProcess');
        return $this->form;
    }

    public function resetPassProcess()
    {
        $presenter = $this->form->getPresenter();
        if ($this->form->isValid()) {
            $values = $this->form->getValues();
            $user = $presenter->getVar('model')->findByPasswordResetToken($values['hash']);

            $values['password_reset_token'] = '';
            $values['password_hash'] = $presenter->getVar('model')->generatePasswordHash($values['password']);

            unset($values['_password'], $values['password'], $values['hash']);
            $user->update($values);

            $values['username'] = $user->username;

            $presenter->emailer->setParams((array)$values);
            $presenter->emailer->quickSend($user->email, 'newpassword.user_registry', $presenter->getBrowser('language'));

            $presenter->flashMessage($presenter->translator->translate('Your password has been updated, please login with it'), 'success');
            $presenter->redirect('signin');
        }
    }

    // ** Rules ********************************************************************************************************
    public function isValueTaken($item, $field)
    {
        switch($field)
        {
            case 'username' :
                return $this->form->getPresenter()->getVar('model')->isUsernameTaken($item->value);
                break;
            case 'email' :
                return $this->form->getPresenter()->getVar('model')->isEmailTaken($item->value);
                break;
        }

    }

    public function checkEmailExists($item)
    {
        return !$this->isValueTaken($item,'email');
    }

    public function isPasswordResetTokenValid($item)
    {
        if($this->form->getPresenter()->getVar('model')->isPasswordResetTokenValid($item->value) == false)
        {
            $this->form->getPresenter()->flashMessage($this->form->getPresenter()->translator->translate('Sorry the validation code is invalid or is expired, please re sent the code to your email'), 'danger');
            $this->form->getPresenter()->redirect('requestpassreset');
        }
        return $this->form->getPresenter()->getVar('model')->isPasswordResetTokenValid($item->value);
    }

}