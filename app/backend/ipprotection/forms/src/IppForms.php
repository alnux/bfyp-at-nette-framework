<?php namespace App\Backend\IpProtection\Forms\Src;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use Nette\Application\UI\Form;
use App\Common\Forms\BaseForm;

class IppForms extends BaseForm
{
	public function WhiteAndBlackForm()
	{
		$this->form->addText('ip', 'Ip number')
			->setRequired('Please insert a number of ip')
			->setAttribute('placeholder', 'Enter a number of ip')
			->addRule(Form::MAX_LENGTH, 'Max %d characters',15)
			->addRule(Form::PATTERN, 'You can only use numbers and dots','[0-9.]+')
			->addRule(callback($this,'validIp'), 'Enter the IP address correctly');
								
		$this->form->addTextArea('reason', 'Reason')
			->setAttribute('rows', 4)
			->setAttribute('placeholder', 'Enter the reason')
			->addRule(Form::MAX_LENGTH, 'Max %d characters',250)
			->addRule(Form::PATTERN, 'Only letters are allowed','[a-zA-ZñÑ ]*');
		
		if(!$this->presenter->getParameter('postId'))
		{   
			$this->form->addSubmit('send', $this->whiteOrBlack()?'Add proxy':'Block ip');
		}
		else
		{
			
			$this->form->addSubmit('send', $this->whiteOrBlack()?'Edit proxy':'Edit Blocked ip');
		}

		$this->form->onSuccess[] = callback($this, 'WhiteAndBlackProcess');
		return $this->form;
	}

	public function WhiteAndBlackProcess()
	{
		
		$values = $this->form->getValues();

		if($this->form->isValid())
		{
			if ($this->presenter->getParameter('postId'))
			{
				$post = $this->presenter->getVar('model')->get($this->presenter->getParameter('postId'));
				$post->update($values);
				$message = $this->presenter->translator->translate($this->whiteOrBlack()?'The proxy has been updated':'The blocked ip has been update');
			}
			else
			{
				$this->presenter->getVar('model')->add($values);
				$message = $this->presenter->translator->translate($this->whiteOrBlack()?'The new proxy has been created':'The ip has been blocked');
			}

			$this->presenter->deleteIpProtectionCache();
			$this->presenter->flashMessage($message, 'success');
			$this->presenter->redirect('default');
		}
		else
		{
			$this->presenter->flashMessage($this->presenter->translator->translate('forms.'.strtolower($this->presenter->getDirecAndTrans()).'.messages.itembroken'), 'danger');
			$this->presenter->redirect('default');
		}
	}

	private function whiteOrBlack()
	{
		return strpos($this->presenter->getName(), 'White')?true:false;
	}
}