<?php namespace App\Backend\IpProtection\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use Nette\Caching\Cache;

use App\Backend\Presenters\BackendBasePresenter;


abstract class IpProtectionBasePresenter extends BackendBasePresenter{

    public function startup()
    {
        parent::startup();

        $this->template->activeipp = 'active';
    }

    /**
	 * delete IpProtectionCache
	 */
	public function deleteIpProtectionCache()
	{
		$cache = new Cache($this->getVar('storage'), $this->settingsValue('cachenamespace'));
		$cache->remove('ipaccess');
	}

}