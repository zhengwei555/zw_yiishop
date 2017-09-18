<?php
/* @var $this yii\web\View */
?>
<div>
    <a href="<?=\yii\helpers\Url::to(['rbac/add-role'])?>"  class="btn btn-default"><span class="glyphicon glyphicon-plus"></span></a>
</div><br>
<table id="table_id_example" class="table table-bordered table-responsive table-hover display">
    <tr>
        <th>角色名称</th>
        <th>角色描述</th>
        <th>操作</th>
    </tr>
    <?php foreach ($roles as $role):?>
        <tr>
            <td><?=$role->name?></td>
            <td><?=$role->description?></td>
            <td><a href="<?=\yii\helpers\Url::to(['rbac/edit-role','name'=>$role->name])?>" class="btn btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
                <a href="<?=\yii\helpers\Url::to(['rbac/delete-role','name'=>$role->name])?>" class="btn btn-default del_btn"><span class="glyphicon glyphicon-trash"></span></a>
            </td>
        </tr>
    <?php endforeach;?>
</table>
