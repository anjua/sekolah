<?php

class RWebUser extends CWebUser
{
    public function afterLogin($fromCookie)
    {
        parent::afterLogin($fromCookie);
    }

    public function checkAccess($operation, $params = array(), $allowCaching = true)
    {
        return $this->isSuperuser === true ? true : parent::checkAccess($operation, $params, $allowCaching);
    }

    public function setIsSuperuser($value)
    {
        $this->setState('Rights_isSuperuser', $value);
    }

    public function getIsSuperuser($value)
    {
        return $this->getState('Rights_isSuperuser');
    }

    public function setRightsReturnUrl($value)
    {
        $this->setState('Rights_returnUrl', $value);
    }

    public function getRightsReturnUrl($defaultUrl = null)
    {
        if($returnUrl = $this->getState('Rights_returnUrl') !== null)
            $this->returnUrl = null;

        return $returnUrl !== null ? CHtml::normalizeUrl($returnUrl) : CHtml::normalizeUrl($defaultUrl);
    }
}