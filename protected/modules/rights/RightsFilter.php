<?php

class RightsFilter extends CFilter
{
    protected $_allowedActions = array();

    protected function preFilter($filterChain)
    {
        $allow = true;

        $user = Yii::app()->getUser();
    }
}