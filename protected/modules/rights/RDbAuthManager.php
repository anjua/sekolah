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
        if($this->_items===array())
            $this->_items = $this->getAuthItems();

        $items = array();
        foreach($this->_items as $name=>$item)
        {
            if(in_array($name, $names))
            {
                if($nested===true)
                    $items[$item->gettype()][$name] = $item;
                else
                    $items[$name] = $item;
            }
        }
        
        return $items;
    }

    public function getAuthItems($type = null, $userId = null, $sort=true)
    {
        if($sort===true)
        {
            if($type===null && $userId===null)
            {
                $sql = "SELECT name, t1.type, description, t1.bizrule, t1.data, weight
                        FROM {$this->itemTable} t1
                        LEFT JOIN {$this->rightsTable} t2 ON name=itemname
                        ORDER BY t1.type DESC, weight ASC";
                $command = $this->db->createCommand($sql);
            }
            elseif($userId===null)
            {
                $sql = "SELECT name, t1.type, description, t1.bizrule, t1.data, weight
                        FROM {$this->itemTable} t1
                        LEFT JOIN {$this->rightsTable} t2 ON name=itemname
					    WHERE t1.type=:type
					    ORDER BY t1.type DESC, weight ASC";
				$command=$this->db->createCommand($sql);
				$command->bindValue(':type', $type);
            }
        }
    }
}