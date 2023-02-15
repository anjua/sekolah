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
        $criteria = new CDbCriteria;
        // bro-tip: $_REQUEST is like $_GET and $_POST combined
        if (isset($_REQUEST['sSearch']) && isset($_REQUEST['sSearch']{0})) {
            // use operator ILIKE if using PostgreSQL to get case insensitive search
            $criteria->addSearchCondition('textColumn', $_REQUEST['sSearch'], true, 'AND', 'ILIKE');
        }

        $sort = new EDTSort('User', $sortableColumnNamesArray);
        $sort->defaultOrder = 'id';
        $pagination = new EDTPagination();

        $dataProvider = new CActiveDataProvider('ModelClass', array(
            'criteria'      => $criteria,
            'pagination'    => $pagination,
            'sort'          => $sort,
        ));

        $widget = $this->createWidget('ext.EDataTables.EDataTables', array(
            'id'            => 'products',
            'dataProvider'  => $dataProvider,
            'ajaxUrl'       => $this->createUrl('/products/index'),
            'columns'       => $columns,
        ));
        if (!Yii::app()->getRequest()->getIsAjaxRequest()) {
            $this->render('index', array('widget' => $widget,));
            return;
        } else {
            echo json_encode($widget->getFormattedData(intval($_REQUEST['sEcho'])));
            Yii::app()->end();
        }

        
    }
}
