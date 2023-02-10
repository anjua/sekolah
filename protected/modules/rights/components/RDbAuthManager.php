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
            elseif( $type===null )
			{
				$sql = "SELECT name,t1.type,description,t1.bizrule,t1.data,weight
					FROM {$this->itemTable} t1
					LEFT JOIN {$this->assignmentTable} t2 ON name=t2.itemname
					LEFT JOIN {$this->rightsTable} t3 ON name=t3.itemname
					WHERE userid=:userid
					ORDER BY t1.type DESC, weight ASC";
				$command=$this->db->createCommand($sql);
				$command->bindValue(':userid', $userId);
			}
			else
			{
				$sql = "SELECT name,t1.type,description,t1.bizrule,t1.data,weight
					FROM {$this->itemTable} t1
					LEFT JOIN {$this->assignmentTable} t2 ON name=t2.itemname
					LEFT JOIN {$this->rightsTable} t3 ON name=t3.itemname
					WHERE t1.type=:type AND userid=:userid
					ORDER BY t1.type DESC, weight ASC";
				$command=$this->db->createCommand($sql);
				$command->bindValue(':type', $type);
				$command->bindValue(':userid', $userId);
			}

			$items = array();
			foreach($command->queryAll() as $row)
				$items[ $row['name'] ] = new CAuthItem($this, $row['name'], $row['type'], $row['description'], $row['bizrule'], unserialize($row['data']));
		}
		// No sorting required.
		else
		{
			$items = parent::getAuthItems($type, $userId);
		}

		return $items;
    }

    public function getItemChildren($names, $allowCaching=true)
    {
        $key = $names===(array)$names ? implode('|', $names) : $names;

        if($allowCaching && isset($this->_itemChildren[$key])===true)
        {
            return $this->_itemChildren[$key];
        }
        else
        {
            if(is_string($names))
            {
                $condition = 'parent='.$this->db->quoteValue($names);
            }
            elseif($names===(array)$names && $names!==array())
            {
                foreach ($names as &$name) 
                    $name = $this->db->quoteValue($name);

                $condition = 'parent IN ('.implode(',', $names).')';
            }
            else
            {
                $condition = '1';
            }

            $sql = "SELECT name, type, description, bizrule, data
                    FROM {$this->itemTable}, {$this->itemChildTable}
                    WHERE {$condition} AND name=child";
            $children = array();
            foreach ($this->db->createCommand($sql)->queryAll() as $row) 
            {
                if($data = @unserialize($row['data'])===false)
                    $data = null;

                $children[$row['name']] = new CAuthItem($this, $row['nama'], $row['type'], $row['description'], $row['bizrule'], $data);
            }

            $children = Rights::getAuthorizer()->attachAuthItemBehavior($children);

            return $this->_itemChildren[$key] = $children;
        }
    }

    public function getAssignmentsByItemName($name)
	{
		$sql = "SELECT * FROM {$this->assignmentTable} WHERE itemname=:itemname";
		$command = $this->db->createCommand($sql);
		$command->bindValue(':itemname', $name);

		$assignments=array();
		foreach($command->queryAll($sql) as $row)
		{
			if(($data=@unserialize($row['data']))===false)
				$data=null;

			$assignments[ $row['userid'] ] = new CAuthAssignment($this, $row['itemname'], $row['userid'], $row['bizrule'], $data);
		}

		return $assignments;
	}

    public function updateItemWeight($result)
	{
		foreach( $result as $weight=>$itemname )
		{
			$sql = "SELECT COUNT(*) FROM {$this->rightsTable}
				WHERE itemname=:itemname";
			$command = $this->db->createCommand($sql);
			$command->bindValue(':itemname', $itemname);

			// Check if the item already has a weight.
			if( $command->queryScalar()>0 )
			{
				$sql = "UPDATE {$this->rightsTable}
					SET weight=:weight
					WHERE itemname=:itemname";
				$command = $this->db->createCommand($sql);
				$command->bindValue(':weight', $weight);
				$command->bindValue(':itemname', $itemname);
				$command->execute();
			}
			// Item does not have a weight, insert it.
			else
			{
				if( ($item = $this->getAuthItem($itemname))!==null )
				{
					$sql = "INSERT INTO {$this->rightsTable} (itemname, type, weight)
						VALUES (:itemname, :type, :weight)";
					$command = $this->db->createCommand($sql);
					$command->bindValue(':itemname', $itemname);
					$command->bindValue(':type', $item->getType());
					$command->bindValue(':weight', $weight);
					$command->execute();
				}
			}
		}
	}
}