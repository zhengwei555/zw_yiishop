<?php
/**
id	primaryKey
name	varchar(50)	名称
intro	text	简介
logo	varchar(255)	LOGO图片
sort	int(11)	排序
status	int(2)	状态(-1删除 0隐藏 1正常)
 */
?>
<div>
    <a href="<?=\yii\helpers\Url::to(['munu/add'])?>"  class="btn btn-default"><span class="glyphicon glyphicon-plus"></span></a>
</div><br>
<table class="table table-bordered table-responsive table-hover">
    <tr>
        <th>ID</th>
        <th>目录名称</th>
        <th>上级菜单</th>
        <th>路由</th>
        <th>排序</th>
        <th>操作</th>
    </tr>
    <?php foreach ($munus as $munu):?>
        <tr data_id="<?=$munu->id?>">
            <td><?=$munu->id?></td>
            <td><?=$munu->name?></td>
            <td><?=$munu->parent_id?></td>
            <td><?=$munu->url?></td>
            <td><?=$munu->sort?></td>
            <td><a href="<?=\yii\helpers\Url::to(['munu/edit','id'=>$munu->id])?>" class="btn btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
                <a href="javascript:; " class="btn btn-default del_btn"><span class="glyphicon glyphicon-trash"></span></a></td>
        </tr>
    <?php endforeach;?>
</table>

<?php
echo \yii\widgets\LinkPager::widget([
    'pagination'=>$pager,
    //'nextPageLabel'=>'下一页',
    //  'prevPageLabel'=>'上一页',
]);
/**
 * @var $this \yii\web\View
 */
$del_url=\yii\helpers\Url::to(['munu/delete']);
//注册Js代码
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
    $('.del_btn').click(function() {
      if(confirm('确定要删除吗?')){
          var tr=$(this).closest('tr');
          var id=tr.attr('data_id');
          $.post('{$del_url}',{id:id},function(data) {
            if(data=='success'){
                alert('删除成功!');
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
