<?php namespace App\Backend\Presenters;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */

/**
 * BlackIp Presenter.
 */

class AppKeyPresenter extends BackendBasePresenter{

    //-- StartUp -----------------------------------------------------------

    public function startup()
    {
        parent::startup();
 
        $this->template->activeappsettings = 'active';
        $this->template->activeappkey = 'active';
               
        $this->template->appkey = $this->user->getStorage()->getIdentity()->__isset('oldappkey')?$this->user->getIdentity()->getData()['oldappkey']:$this->settingsValue('appkey');
        
        $this->template->appkeyaction = "changeappkey1";

         $this['breadCrumb']->addLink($this->translator->translate("App Key"));
    }
    //-----------------------------------------------------------------------
    
    //-- Renders ------------------------------------------------------------
    public function renderDefault()
    {
        $this->template->title = $this->translator->translate("App Key");
        $this->template->changekey = $this->translator->translate("Change value (step 1)");
    }

    public function renderChangeAppKey1()
    {
        $this->template->title = $this->translator->translate("Setup app key (step 1)");

        $this->template->appkeyaction = "changeappkey2";
        $this->template->changekey = $this->translator->translate("Change value (step 2)");
        
        $this['breadCrumb']->editLink($this->translator->translate("App Key"),$this->link('AppKey:'));
        $this['breadCrumb']->addLink($this->translator->translate("Setup app key (step 1)"));
        
        $this->setView('default');
    }
    // -- Actions ------------------------------------------------------------
    public function actionDefault()
    {
        if($this->user->getStorage()->getIdentity()->__isset('oldappkey'))
        {
            $this->user->getStorage()->getIdentity()->__set('oldappkey', null);
        }
    }

    public function actionChangeAppKey1()
    {
        if($this->user->getIdentity()->getData()['username'] == $this->settingsValue('superuser'))
        {
            if(!$this->user->getStorage()->getIdentity()->__isset('oldappkey'))
            {   
                $this->user->getStorage()->getIdentity()->__set('oldappkey', $this->settingsValue('appkey'));    
                $this->flashMessage($this->translator->translate("Please change the 'AppKey' value on config.neon file and press 'Change value (step 2)' button. If everything goes OK you will logout from application"), 'success');
            }

            elseif($this->user->getIdentity()->getData()['oldappkey']==$this->settingsValue('appkey'))
            {
                $this->flashMessage($this->translator->translate("Please change the 'AppKey' value on config.neon file and press 'Change value (step 2)' button. If everything goes OK you will logout from application"), 'danger');
            }
        }
        else
        {
            $this->flashMessage($this->translator->translate("Sorry but you can not change the key of the application because you are not the superadministrator"), 'danger');
            $this->redirect('AppKey:');
        }
    }

    public function actionChangeAppKey2()
    {
        if(!$this->user->getStorage()->getIdentity()->__isset('oldappkey') || $this->user->getIdentity()->getData()['username'] != $this->settingsValue('superuser'))
        {
            $this->redirect('AppKey:'); 
        }

        foreach ($this->settingsValue('tables') as $table => $fields) 
        {
            if(method_exists($this->getVar('modeler'),'get'.$table))              
            {       
                $catch = $this->getVar('modeler')->{'get'.$table}()->catchBadColumns($fields);
                if($catch==false)
                {
                    $models[] = array($this->getVar('modeler')->{'get'.$table}(), $fields);
                }
                else
                {
                    $columnError = isset($columnError)?$columnError.$table.':'.$catch.'; ':$table.':'.$catch.'; ';
                }
            }
            else
            {
                $tableError = isset($tableError)?$tableError.$table.', ':$table.', ';               
            }
        }

        if(isset($tableError)||isset($columnError)||$this->user->getIdentity()->getData()['oldappkey']==$this->settingsValue('appkey'))
        {
            !isset($tableError)?:$this->flashMessage($this->translator->translate("The following tables do not exist:").substr($tableError,0,-2),'danger');
            !isset($columnError)?:$this->flashMessage($this->translator->translate("There is an error in the declaration of the following columns").substr($columnError,0,-2),'danger');
            $this->redirect('AppKey:changeappkey1');
        }

        $newAppKey = $this->settingsValue('appkey');
        foreach($models as $model)
        {
            $veriField = array_pop($model[1]);
            foreach($model[0]->all() as $row)
            {
                $newCode ='';
                for($i=0;$i<count($model[1])-1;$i++)
                {
                    $newCode.= $row->{$model[1][$i]}; 
                }
                $newCode.= $newAppKey.$row->{end($model[1])};
                $value[$veriField]=sha1($newCode);
                $row->update($value);
            }
        }
        $this->deleteAclCache();
        $this->redirect(':Common:Sign:logout');
    }

}
