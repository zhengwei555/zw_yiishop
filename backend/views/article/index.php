<?php
/**
name	varchar(50)	名称
intro	text	简介
sort	int(11)	排序
status	int(2)	状态(-1删除 0隐藏 1正常)
 */
?>
<div>
    <a href="<?=\yii\helpers\Url::to(['article/add'])?>"  class="btn btn-default"><span class="glyphicon glyphicon-plus"></span></a>
</div><br>
<table class="table table-bordered table-responsive table-hover table-striped">
    <tr>
        <th>ID</th>
        <th>文章名称</th>
        <th>文章简介</th>
        <th>文章分类id</th>
        <th>文章排序</th>
        <th>文章状态</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    <?php foreach ($articles as $article):?>
        <tr data_id="<?=$article->id?>">
            <td><?=$article->id?></td>
            <td><?=$article->name?></td>
            <td><?=$article->intro?></td>
            <td><?=$article->article_category_id?></td>
            <td><?=$article->sort?></td>
            <td><?=$article->status?'显示':'隐藏'?></td>
            <td><?=date('Y-m-d ',$article->create_time)?></td>
            <td><a href="<?=\yii\helpers\Url::to(['article/detail','id'=>$article->id])?>" class="btn btn-default"><span class="glyphicon glyphicon-eye-open"></span></a>
            <a href="<?=\yii\helpers\Url::to(['article/edit','id'=>$article->id])?>" class="btn btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
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
$del_url=\yii\helpers\Url::to(['article/delete']);
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
