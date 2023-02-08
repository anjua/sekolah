<?php

class RDbAuthManager extends CDbAuthManager
{
    public $rightsTable = 'Rights';

    private $_item = array();
    private $_itemChildren = array();

    public function addItemChild($itemName, $childName)
    {
        if($this->hasItemChild($itemName, $childName)===false)
            return parent::addItemChild($itemName, $childName);
    }
}