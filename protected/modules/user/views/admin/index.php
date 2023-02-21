<?php
$this->contentHeader = ucfirst($this->getModule()->id);
$this->smallContentHeader = $this->id == 'admin' ? "Manajemen" : $this->id;

$this->breadcrumbs = array(
    'User' => array('admin'),
    'Index',
);
?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo '<span>' . CHtml::link('<span>' . Yii::t('user', 'Create User') . '</span>', array('/user/admin/create'), array('class' => 'btn btn-primary')) . '</span>'; ?></h3>
    </div>
    <div class="box-body">
        <table id="user_table" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Last Visit At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($model as $value) {; ?>
                    <tr>
                        <td><?php echo User::model()->name($value, null); ?></td>
                        <td><?php echo User::model()->role($value, null); ?></td>
                        <td><?php echo $value->email; ?></td>
                        <td><?php echo $value->lastvisit_at; ?></td>
                        <td>
                            <button type="button" class="btn btn-primary btn-xs dt-edit" style="margin-right:16px;">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                            </button>
                            <button type="button" class="btn btn-danger btn-xs dt-delete">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                            </button>
                        </td>
                    </tr>
                <?php }; ?>
            </tbody>
        </table>
    </div>
    <div class="box-footer">

    </div>
</div>

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Row information</h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>