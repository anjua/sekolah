<?php 
$this->contentHeader = ucfirst($this->getModule()->id);
$this->smallContentHeader = $this->id=='admin'?"Manajemen":$this->id;

$this->breadcrumbs= array(
    'User'=>array('admin'),
    'Index',
);
?>

<div class="box box-primary">
    <div class="box-header with-border">
        <!-- <h3 class="box-title">Manajemen User</h3> -->
    </div>
    <div class="box-body">

    </div>
    <div class="box-footer">
        <table id="user_table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                <tr></tr>
            </tbody>
        </table>
    </div>
</div>