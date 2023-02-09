<?php

class RDbAuthManager extends CDbAuthManager
{
    public $rightsTable = 'Rights';

    private $_items = array();
    private $_itemChildren = array();

    public function addItemChild($itemName, $childName)
    {
        if($this->hasItemChild($itemName, $childName)===false)
            return parent::addItemChild($itemName, $childName);
    }

    public function assign($itemName, $userId, $bizRule = null, $data = null)
    {
        if ($this->getAuthAssignment($itemName, $userId)===null) 
        {
            return parent::assign($itemName, $userId, $bizRule, $data);
        }
    }

    public function getAuthItem($name, $allowCaching=true)
    {
        if ($allowCaching && $this->_items===array()) 
            $this->_items = $this->getAuthItems();

        if($allowCaching && isset($this->_items[$name]))
        {
            return $this->_items[$name];
        }
        elseif($item=parent::getAuthItem($name)!==null)
        {
            return $item;
        }

        return null;
    }

    public function getAuthItemsByNames($names, $nested=false)
    {
        
    }
}