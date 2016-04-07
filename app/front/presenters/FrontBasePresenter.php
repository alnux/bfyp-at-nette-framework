<?php namespace App\Front\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015 
 */
use App\Common\Presenters\BasePresenter;


abstract class FrontBasePresenter extends BasePresenter{

	
	/** @inject @var \GeoIp2\Database\Reader  */
	public $geoip;

	public function startup()
    {
        parent::startup();
        
        // ** Country name and iso code**
        if(!$this->globalSession->getSection('Global')->__isset("country"))
        {     	
        	try
			{
				$record = $this->geoip->country($this->getBrowser('ip')); 
				if(in_array($record->country->isoCode, $this->settingsValue('location.countries_front_templates')))
				{
					$this->globalSession->getSection('Global')->country['iso'] = $record->country->isoCode;
					$this->globalSession->getSection('Global')->country['name'] = $record->country->name;  
				}
				else
				{
					$this->globalSession->getSection('Global')->country = $this->settingsValue('location.country_default');
				}

			}
			catch(\GeoIp2\Exception\AddressNotFoundException $e)
			{
				$this->globalSession->getSection('Global')->country = $this->settingsValue('location.country_default');
			}
        }
        // ***********
                
        $this->bcrumbvar = ':Front:Index:default';

        $this->template->menu = 'frontmenu';
    }

    //-- Parent methods overwrite-------------------------------------------------------------------------
    /**
	 * Here overload formatTemplateFiles to add separate templates by country 
	 * eg: CZ have diferent templates from US. It does not alter before templates 
	 * calling, so you can still work with root template folder.
	 * Into special country folder you just work with latte files. 
	 * 
	 * @return array
	 */
	public function formatTemplateFiles()
	{
		$files = parent::formatTemplateFiles();
		$country = $this->globalSession->getSection('Global')->country['iso'];
		$name = $this->getName();
		$presenter = substr($name, strrpos(':' . $name, ':'));
		$dir = dirname($this->getReflection()->getFileName());
		$dir = is_dir("$dir/templates") ? $dir : dirname($dir);

		$files[] = "$dir/templates/$country/$presenter/$this->view.latte";

		return $files;
	}
	//----------------------------------------------------------------------------------------------------

    
}
