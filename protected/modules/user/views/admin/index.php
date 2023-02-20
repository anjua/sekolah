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

                        </td>
                    </tr>
                <?php }; ?>
            </tbody>
        </table>
    </div>
    <div class="box-footer">

    </div>
</div>