<?php namespace App\Common\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use Nette,
	Nette\Application\UI\Presenter,
	Nette\Caching\Cache;

use App\Backend\Acl\Classes\AclTreeMaker;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter
{
	/**
	 * DI language
	 * @var \LiveTranslator\Translator
	 */
	/** @inject @var \LiveTranslator\Translator */
	public $translator;

	/**
	 * DI Provide all forms
	 * @var \App\Common\Classes\FormsContainer
	 */
	/**  @inject @var \App\Common\Classes\FormsContainer */
	public $forms;

	/**
	 * Service setup call parameters.settings params
	 * @var \App\Common\Classes\SettingsCaller
	 */
	private $setup;

	/**
	 * DI Provide models
	 * @var \App\Common\Classes\ModelsContainer
	 */
	private $modeler;

	/**
	 * DI Storage
	 * @var \Nette\Caching\IStorage
	 */
	private $storage;

	/**
	 * Cache of ACL
	 * @var Nette\Caching\Cache
	 */
	private $aclCache;
	
	/**
	 * App session vars
	 * @var \Nette\Http\Session
	 */
	protected $globalSession;

	/**
	 * Specific presenter model
	 * @var Model
	 */
	protected $model;

	/**
	 * breadCrumb global var
	 * @var \Alnux\NetteBreadCrumb\BreadCrumb
	 */
	protected $bcrumbvar;

	/** 
	 * Complete and static dir to layout
	 * @var bool
	 */
    private $staticLayout;

	public function startup()
	{
		parent::startup();

		// ** Global session vars **
		$globalSection = $this->globalSession->getSection('Global');
		if(!$globalSection->__isset("language"))
		{
			$globalSection->language = $this->getBrowser('language');
		}
		//**************************

		// ** Set app language **
		$this->translator->setCurrentLang($this->globalSession->getSection('Global')->language);
		// **********************
		
		// ** Enabled languages **  
		$this->template->languages = $this->settingsValue('location.enabled_languages');
		//************************

		// ** Init access control list cached **
     	$this->generateAclCache();
		// *************************************

		// ** Site name **
		$this->template->site_title = $this->settingsValue('site_title');
		// ***************
		
		// ** User loggedIn global vars ****
		if($this->user->isLoggedIn())
		{
			if(!$this->user->getStorage()->getIdentity()->__isset('menuitemsaccess'))
			{ 
				$this->user->getStorage()->getIdentity()->__set('menuitemsaccess', $this->generateAuthUserMenu());
				if(empty($this->user->getStorage()->getIdentity()->getData()['menuitemsaccess']))
				{
					$this->redirect( $this->settingsValue('no_resources'));
				}
			}

			
			$this->template->menuitemsaccess = $this->user->getStorage()->getIdentity()->getData()['menuitemsaccess'];
			$this->template->userdata = $this->user->getIdentity()->getData();
	
		}
		// **********************************
		
	}

	//-- Handles------------------------------------------------------------------------------------------------
	public function handleChangeLanguage($lang)
	{
		if(in_array($lang, $this->settingsValue('location.enabled_languages')))
		{
			$this->globalSession->getSection('Global')->language = $lang;
		}
		$this->redirect('this');
	}

	// -- Components ----------------------------------------------------------------------------------

	protected function createComponentBreadCrumb()
	{
		$breadCrumb = new \Alnux\NetteBreadCrumb\BreadCrumb;

		$breadCrumb->addLink($this->translator->translate('Home'), $this->link($this->bcrumbvar));

		return $breadCrumb;
	}

	// -- Services ------------------------------------------------------------------------------------

	public function injectSettingsCaller(\App\Common\Classes\SettingsCaller $setup)
	{
		$this->setup = $setup;
	}

	public function injectModelsContainer(\App\Common\Classes\ModelsContainer $modelContainer)
	{
		$this->modeler = $modelContainer;
	}

	public function injectCacheStorage(\Nette\Caching\IStorage $cacheStorage)
	{
		$this->storage = $cacheStorage;
	}

	public function injectSession(\Nette\Http\Session $globalSession)
	{
		$this->globalSession = $globalSession;
	}

	
	// -- Getters --------------------------------------------------------------------------------------

	public function model($model)
	{
		return $this->modeler->{'get'.$model}();
	}

	public function settingsValue($param)
	{
		return $this->setup->getParam($param);
	}

	public function getBrowser($value)
	{
		switch($value)
		{
			case 'language':
				return $this->getHttpRequest()->detectLanguage($this->settingsValue('location.enabled_languages'))?:$this->settingsValue('location.language_default');
				break;
			case 'ip':
				return $this->getHttpRequest()->getRemoteAddress();
				break;
			case 'url':
				return $this->getHttpRequest()->getUrl();
				break;
		}
	}

	public function getVar($var)
	{
		return $this->{$var};
	}

	// -- Parent methods overwrite --------------------------------------------------------------------
	protected function createTemplate()
	{
		$template = parent::createTemplate();
		$template->setTranslator($this->translator);
		return $template;
	}

	protected function createComponent($name)
	{
		$component = parent::createComponent($name);
		if ($component instanceof \Nette\Forms\Form)
		{
			$component->setTranslator($this->translator);
		}
		return $component;
	}

	
	/**
	* Changes or disables layout.
	* @param  string|FALSE
	* @return self
	*/
	public function setLayout($layout, $staticpath = FALSE)
	{
		$this->layout = $layout === FALSE ? FALSE : (string) $layout;
		$this->staticLayout = $staticpath;
		return $this;
	}

	/**
	  * Finds layout template file name.
	  * @return string
	  * @internal
	 */
	public function findLayoutTemplateFile()
	{
		if ($this->layout === FALSE) {
			return;
		}
		
		if($this->staticLayout === FALSE) {
			$files = $this->formatLayoutTemplateFiles();
			
			foreach ($files as $file) {
				if (is_file($file)) {
					return $file;
				}
			}
		}
		else {
			if (is_file($this->layout)) {
				return $this->layout;
			}	
		}

		if ($this->layout) {
			$file = preg_replace('#^.*([/\\\\].{1,70})\z#U', "\xE2\x80\xA6\$1", reset($files));
			$file = strtr($file, '/', DIRECTORY_SEPARATOR);
			throw new Nette\FileNotFoundException("Layout not found. Missing template '$file'.");
		}
	}
	
	protected function beforeRender()
    {
        parent::beforeRender();
        $this->setLayout(APP_DIR.'/common/templates/@layout.latte', TRUE);
    }

	// -- More important app methods ---------------------------------------------------------------------
	
	/**
	 * Generate Access control list cache for users
	 */
	public function generateAclCache()
	{
		$this->aclCache = new Cache($this->storage, $this->settingsValue('cachenamespace'));
		$acl = $this->aclCache->load('acl');
		if ($acl===NULL)
		{
			$acl = new AclTreeMaker($this->modeler, $this->setup);
			$this->aclCache->save('acl', $acl, array(
				Cache::EXPIRE => '60 minutes',
				Cache::FILES => APP_DIR.'/config/config.neon'
				));
		}
		$this->user->setAuthorizator($acl);
	}

	/**
	 * Generate authenticated user menu view options
	 * @return array
	 */
	private function generateAuthUserMenu()
	{
		$menuitemsaccess = array();
		foreach($this->user->getAuthorizator()->getResources() as $resource)
		{
			if($this->user->isAllowed($resource, 'view'))
			{
				$menuitemsaccess[$resource]=1;
				
				foreach($this->user->getAuthorizator()->getResourceParents($resource) as $parent => $value)
				{
					if(!isset($menuitemsaccess[$value]) && $value !=="")
					{
						$menuitemsaccess[$value]=1;
					}
				}
			}
		}
		return $menuitemsaccess;
	}

	/**
	 * delete AclCache
	 */
	public function deleteAclCache()
	{
		$this->aclCache->remove('acl');
	}

	/**
    * Check if the user has permissions to enter this section.
    * If not, then it is redirected to sigin.
    */
	public function checkAccess($privilege = 'view', $redirectAction = true, $redirect = 'Home:')
	{
		$view = true;

		if($this->user->isLoggedIn())
		{	
			if (!$this->user->isAllowed(strtolower(str_replace("\\", "_", get_class($this))), $privilege)) 
			{
			 	$this->user->getStorage()->getIdentity()->__set('menuitemsaccess', $this->generateAuthUserMenu()?:'');
			 	if(empty($this->user->getStorage()->getIdentity()->getData()['menuitemsaccess']))
				{
					$this->redirect( $this->settingsValue('no_resources'));
				}
			 	if($redirectAction)
			 	{
			 		$this->redirect(':Front:Users:Index:default');
			 	}
			 	else
			 	{
			 		$view = false;
			 	}
			}

		}
		else
		{
			$this->redirect(':Common:Sign:signin', array('backlink' => $this->storeRequest()));
		}

		return $view;
	}

	
}
