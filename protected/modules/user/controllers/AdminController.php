<?php

class AdminController extends Controller
{
    public $defaultAction = 'admin';
    public $layout = '//layouts/mainlte';

    private $_model;

    // public function filters()
    // {
    //     return CMap::mergeArray(parent::filters(), array('accessControl'));
    // }

    // public function accessRules()
    // {
    //     return array(
    //         array(
    //             'allow',
    //             'actions' => array('admin', 'delete', 'create', 'update', 'view'),
    //             'users' => UserModule::getAdmins(),
    //         ),
    //         array(
    //             'deny',
    //             'users' => array('*'),
    //         ),
    //     );
    // }

    public function actionAdmin()
    {
        $model = User::model()->findAll();
        //$model->unsetAttributes();
        if(isset($_GET['User']))
            $model->attributes=$_GET['User'];
        // $model = new User();
        // var_dump($model);
        // die();

        $this->render('index', array('model'=>$model));
    }
}
