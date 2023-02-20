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
        $model = new User('search');
        $model->unsetAttributes();
        // $model = new User();
        // var_dump($model);
        // die();

        $this->render('index', array('model'=>$model));
    }
}
