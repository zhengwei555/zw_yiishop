<?php
/* @var $this yii\web\View */
?>
<div>
    <a href="<?=\yii\helpers\Url::to(['admin/add'])?>" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span></a>
</div><br>
<table class="table table-bordered table-responsive table-hover">
    <tr>
        <th>ID</th>
        <th>用户名</th>
        <th>邮箱</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach ($admins as $admin):?>
        <tr data_id="<?=$admin->id?>">
            <td><?=$admin->id?></td>
            <td><?=$admin->username?></td>
            <td><?=$admin->email?></td>
            <td><?=$admin->status?'启用':'禁用'?></td>
            <td><a href="<?=\yii\helpers\Url::to(['admin/edit','id'=>$admin->id])?>" class="btn btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
               <a href="javascript:; " class="btn btn-default del_btn"><span class="glyphicon glyphicon-trash"></span></a></td>
        </tr>
    <?php endforeach;?>
</table>

<?php
    echo \yii\widgets\LinkPager::widget([
            'pagination'=>$pager,
    ]);

    $del_url=\yii\helpers\Url::to(['admin/delete']);
    $this->registerJs(new \yii\web\JsExpression(
        <<<JS
    $('.del_btn').click(function() {
      if(confirm('确定删除吗?')){
          var tr=$(this).closest('tr');
          var id=tr.attr('data_id');
          $.post('{$del_url}',{id:id},function(data) {
            if(data=='success'){
                alert('删除成功');
                tr.hide('slow');
            }else {
                alert('删除失败');
            }
          });
      }
    })
JS

    ));

?>
