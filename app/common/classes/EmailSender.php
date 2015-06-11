<?php namespace App\Common\Classes;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */

use \Nette\Object,
	\Nette\Mail\Message,
	\Nette\Mail\IMailer,
	\Latte\Engine;

use \App\Common\Classes\ModelsContainer,
	\App\Common\Classes\SettingsCaller;

/**
* Class for send messages
*/
class EmailSender extends Object
{
  	private $mailer;
	private $message;
	private $setup;
	protected $params;
	protected $custom_params = NULL;
	private $template;


	public function __construct(IMailer $mailer, SettingsCaller $setup)
	{
		$this->message = new Message;
		$this->mailer = $mailer;
		$this->setup = $setup;
		$this->init();

	}

	public function setTemplate($latteObject)
	{
		$this->template = clone $latteObject;
	}

	/**
	 * Initial sets of email message.
	 * Init local params and from head message
	 */
	private function init()
	{
		$this->params =[
			'site_title' => $this->setup->getParam('site_title')
		];

		$this->from($this->setup->getParam('mailer.email_mailer'),$this->setup->getParam('site_title'));
	}

	/**
	 * Merge local params and custom params. the local params cant be overwrite
	 * @param array $params
	 */
	protected function mergeParams()
	{

		if($this->custom_params != NULL)
		{
			// Attach second elements to First / Anexa los elementos del segundo array al primero
			$this->params = $this->params + $this->custom_params;
		}
	}


	/**
	 * Set parameter to the template, so you can pass complete
	 * array and unset witch you want declaring the second argumet
	 * with spaces or comma
	 * @param string $params
	 * @param string null $exclude
	 */
	public function setParams($params, $exclude=NULL)
	{
		if($exclude != NULL)
		{
			foreach (preg_split("/[\s,]+/", $exclude)as $value)
			{
				if(array_key_exists($value, $params))
				{
					unset($params[$value]);
				}
			}
		}
		$this->custom_params = $params;
	}

	/**
	 * Build message header
	 * @param string $email
	 * @param string $name
	 * @return string
	 */
	protected function buildMailHead($email, $name='')
	{
		$from = '__email__';
		if($name!='')
		{
			$from = $name.' <'.$from.'>';
		}
		$from = str_replace('__email__', $email, $from);
		return $from;
	}

	/**
	 * From header
	 * @param string $email
	 * @param string $name
	 */
	public function from($email, $name='')
	{
		$this->message->setFrom($this->buildMailHead($email, $name));
	}

	/**
	 * To header
	 * @param string$email
	 * @param string $name
	 */
	public function addTo($email, $name='')
	{
		$this->message->addTo($this->buildMailHead($email, $name));
	}

	/**
	 * Message subject
	 * @param string $subject
	 */
	public function subject($subject)
	{
		$this->message->setSubject($subject);
	}

	/**
	 * Text message body
	 * @param string $body
	 */
	public function textBody($body)
	{
		$this->message->setBody($body);
	}

	/**
	 * Html message body
	 * @param string $body
	 */
	public function htmlBody($body)
	{
		$this->message->setHTMLBody($body);
	}

	/**
	 * VERY IMPORTANT : To use this you have to clone presenter->getTemplate() function on presenter startup or before
	 * to use this method, you ca do it by setTemplate($latteObject) where $latteObject is a
	 * \Nette\Bridges\ApplicationLatte\Template instance
	 * @param string $template
	 * @param string null $language
	 */
	public function htmlTemplate($file, $language = NULL)
	{
		if($language == NULL || in_array($language, $this->setup->getParam('location.enabled_languages'))==false)
		{
			$language = $this->setup->getParam('location.language_default');
		}

		$this->mergeParams();
		$htmlFile = $this->setup->getParam('mailer.email_templates_path') . '/' . $language . '/' . $file . '.latte';

		$this->template->setFile($htmlFile);
		$this->template->setParameters($this->params);

		$this->htmlBody((string) $this->template);
	}

	/**
	 * Send the message
	 */
	public function send()
	{
		$this->mailer->send($this->message);

	}

	/**
	 * Quick message send
	 *
	 * @param string $receiver
	 * @param string $template
	 * @param string null $language
	 */
	public function quickSend($receiver, $template, $language = NULL)
	{
		$this->addTo($receiver);
		$this->htmlTemplate($template, $language);
		$this->send();
	}
}
