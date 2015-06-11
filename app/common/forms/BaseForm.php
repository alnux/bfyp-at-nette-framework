<?php namespace App\Common\Forms;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use \Nette\Object,
    Nette\Application\UI\Form;

abstract class BaseForm extends Object{

    protected $form;
    protected $presenter;

    public function __construct($presenter)
    {
        $this->presenter = $presenter;

        $this->form = new Form;

        $this->form->addProtection('The form was broken please try again');
    }

    /**
     * Check if item is unique on table
     * @param  Form::addText $item  var catchs from form
     * @param  string $field In this case is 'key' or 'name'
     * @return boolean
     */
    public function unique($item,$field)
    {
        return !$this->presenter->getVar('model')->unique($field,$item->value);
    }

    /**
     * Check if item is only one exepting self
     * @param  Form::addText $item  vars from form
     * @param  array $fieldid has the field and id to verificate
     * @return boolean
     */
    public function editExceptingSelf($item,$fieldid)
    {
        return !$this->presenter->getVar('model')->exeptingSelf($fieldid['field'], $fieldid['id'], $item->value);

    }

    /**
     * Verificate if ip is on valid format
     * @param  Form::addText $item  var catchs from form
     * @return boolean
     */
    public function validIp($item)
    {
        return filter_var($item->value, FILTER_VALIDATE_IP);
    }

}