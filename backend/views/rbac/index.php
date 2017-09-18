<?php
/* @var $this yii\web\View */

$this->registerCssFile('http://cdn.datatables.net/1.10.15/css/jquery.dataTables.css')
?>
<div>
    <a href="<?=\yii\helpers\Url::to(['rbac/add'])?>"  class="btn btn-default"><span class="glyphicon glyphicon-plus"></span></a>
</div><br>
    <table id="table_id_example" class="table table-bordered table-responsive table-hover display">
        <thead>
        <tr>
            <th>权限名称</th>
            <th>权限描述</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($permissions as $permission):?>
            <tr>
                <td><?=$permission->name?></td>
                <td><?=$permission->description?></td>
                <td><a href="<?=\yii\helpers\Url::to(['rbac/edit','name'=>$permission->name])?>" class="btn btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
                    <a href="<?=\yii\helpers\Url::to(['rbac/delete','name'=>$permission->name])?>" class="btn btn-default del_btn"><span class="glyphicon glyphicon-trash"></span></a>
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
<?php
$this->registerJsFile('http://cdn.datatables.net/1.10.15/js/jquery.dataTables.js',['depends'=>\yii\web\JqueryAsset::className()]);
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
    $(document).ready( function () {
     $('#table_id_example').DataTable();
    } );
JS
));
