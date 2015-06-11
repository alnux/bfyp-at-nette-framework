<?php namespace App\Backend\Acl\Model;
/**
 * @author Leonardo Allende <alnux@ya.ru> 2015
 */
use App\Common\Model\BaseModel;

class AclBaseModel extends BaseModel{

    /**
     * all childs of parent on array
     *
     * @param   integer Parent id
     * @param   array Array of curent roles
     * @param   integer Depth of tree structure
     * @param   integer id of regular resource
     */
    public function getParents($parent_id, &$array, $depth, $id, $idChilds)
    {
        $rows = $this->context->table($this->table)
            ->select('id, parent_id, name')
            ->where(is_null($parent_id)?'parent_id IS NULL':'parent_id ='.$parent_id)
            ->order('id');

        foreach ($rows as $row)
        {

            $array[$row->id] = \Nette\Utils\Html::el('option')->value($row->id)->setHtml(($depth ? str_repeat('--', $depth) : '').' '.$row->name);
            if (in_array($row->id, $idChilds))
            {
                $array[$row->id]->disabled(TRUE);
                $array[$row->id]->__set('style','background: #F7F5F5; color: #f00;');
            }

            $this->getParents($row->id, $array, ($depth+1), $id, $idChilds);
        }

    }

    /**
     * Get all childs of a parent into an array
     * @param  int $parent_id
     * @param  array $childs
     * @return array $childs all tree childs parent
     */
    public function getAllTreeChildNodesOnArray($parent_id = NULL, &$childs)
    {
        $rows = $this->all()->where(is_null($parent_id) ? 'parent_id IS NULL' : 'parent_id='.$parent_id);
        foreach ($rows as $row) {
            $childs[] = $row->id;
            $this->getAllTreeChildNodesOnArray($row->id, $childs);
        }

    }

    /**
     * Return all children of specific parent of node
     *
     * @param   integer Parent id
     * @return  object
     */
    public function getChildNodes($parent_id = NULL)
    {
        return $this->all()->where(is_null($parent_id) ? 'parent_id IS NULL' : 'parent_id='.$parent_id)->order('name');
    }

    /**
     * Parent has node children?
     *
     * @param   integer Parent id
     * @return  integer Number of children
     */
    public function hasChildNodes($parent_id, &$count = 0)
    {
        return $this->customField($parent_id, 'parent_id')->count('*');
    }

    /**
     * Checks if it has permission relation
     * or if in case role checks on users roles
     * @param  int  $id
     * @return boolean
     */
    public function hasPermissionRelation($id, $field)
    {
        $table = \App\Config\TablesSettings::T_PERMISSION;
        $rows = array(NULL,$id);
        $check = false;
        foreach ($rows as $row)
        {
            if($check = (bool)$this->context->table($table)->select('*')->where($field, $row)->fetch())
            {
                break;
            }
        }
        if($this->roleResourceField == 'role_id')
        {
            $check = (bool)$this->customFieldAndTable($id, $this->roleResourceField, \App\Config\TablesSettings::T_USER_ROLE )->fetch();
        }
        return $check;
    }

    /**
     * Check if parent_id of child is equals to id of parent,
     * use it if you want avoid a child as parent
     * @param  integer $id
     * @param  integer $childId
     * @return bolean
     */
    public function avoidChildAsParent($id, $childId)
    {
        return $this->customField($childId)->fetch()->parent_id == $id;
    }

    /**
     * Return structured tree
     *
     * @return  array
     */
    public function getTreeValues($id = NULL)
    {
        $tree[]='No parent';
        $idChilds[] = (int)$id;
        if($id != NULL)
        {
            $this->getAllTreeChildNodesOnArray($id, $idChilds);
        }

        $this->getParents(NULL, $tree, 0, $id, $idChilds);

        return $tree;
    }
}