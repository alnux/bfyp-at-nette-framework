<?php namespace App\Common\Model;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use \Nette\Object,
    \Nette\Database\Context as Context;

abstract class BaseModel extends Object
{
    protected $context;
    protected $table;
    
    public function __construct(\Nette\DI\Container $container)
    {
        $this->context = $container->getService('database.default.context');
    }

    /**
     * Return all items
     *
     * @return \Nette\Database\Table\Selection
     */
    public function all()
    {
        return $this->context->table($this->table);
    }

    /**
     * Get specific item
     *
     * @param integer Privilege ID
     * @return \Nette\Database\Row
     */
    public function get($id)
    {
        return $this->context->table($this->table)->get($id);
    }

    /**
     * Add new item
     *
     * @param array $values
     */
    public function add($values)
    {
        return $this->context->table($this->table)->insert($values);
    }

    /**
     *
     * Delete item
     *
     * @param integer item ID
     */
    public function delete($id)
    {
        $this->context->table($this->table)->where('id', $id)->delete();
    }

    /**
     * Make a timestamp
     * @return string
     */
    public function timestamp()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     *
     * catch data from any field, by default id
     *
     * @param  string $field
     * @param  integer $value
     */
    public function customField($value, $field='id')
    {
        return $this->context->table($this->table)->select('*')->where($field, $value);
    }

    /**
     *
     * catch data from any field, by default id
     *
     * @param  string $field
     * @param  integer $value
     */
    public function customFieldAndT($value, $field, $table, $select ='*')
    {
        return $this->context->table($table)->select($select)->where($field, $value);
    }

    /**
     * This function do the same as php toArray but just replace
     * Null results by 0
     * @param  int $id
     * @return array
     */
    public function getOnArray($id)
    {
        $result = false;
        $rows = $this->get($id);
        if($rows)
        {
            foreach ($rows as $row => $value)
            {
                $result[$row] = $value?$value:0;
            }
        }
        return $result;
    }

    /**
     *
     * @param  string $field
     * @param  mixed $var
     * @return boolean
     */
    public function unique($field,$var)
    {
        return (bool)$this->customField($var,$field)->fetch();
    }

    /**
     *
     * @param  string $field
     * @param  integer $id
     * @param  mixed $val
     * @return boolean
     */
    public function exeptingSelf($field,$id,$val)
    {
        $switch = false;
        $row=$this->customField($val,$field)->fetch();
        if($row!=false)
        {
            if($row->id!=$id)
            {
                $switch = true;
            }
        }

        return $switch;
    }

    /**
     *
     * catch data from any field and any table
     *
     * @param  string $field
     * @param  integer $value
     */
    public function customFieldAndTable($value, $field, $table, $select ='*')
    {
        return $this->context->table($table)->select($select)->where($field, $value);
    }

    /**
     * Search if give columns into array exist into the table
     * if does not exist it returns a string with the name of 
     * columns that are not in the table other way return FALSE
     * @param  array $columns
     * @return bool|string
     */
    public function catchBadColumns($columns)
    {
        $tableColumns = $this->context->getConnection()->getSupplementalDriver()->getColumns($this->table);
        $values=array();
        $errorColumns = '';
        foreach ($tableColumns as $tableColumn => $value) {
            $values[] = $value['name'];
        }
        foreach ($columns as $column) {
            if(!in_array($column, $values))
            {
                $errorColumns.=$column.', ';
            }       
        }
        return substr($errorColumns, 0, -2);
    }



}