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
    <a href="<?=\yii\helpers\Url::to(['goods-category/add'])?>"  class="btn btn-default"><span class="glyphicon glyphicon-plus"></span></a>
</div><br>
<table class="table table-bordered table-responsive table table-hover">
    <tr>
        <th>ID</th>
        <th>商品名称</th>
        <th>上级分类</th>
        <th>简介</th>
        <th>操作</th>
    </tr>
    <?php foreach ($goodscategorys as $goodscategory):?>
        <tr data_id="<?=$goodscategory->id?>">
            <td><?=$goodscategory->id?></td>
            <td><?=$goodscategory->name?></td>
            <td><?=$goodscategory->parent_id?></td>
            <td><?=$goodscategory->intro?></td>
            <td><a href="<?=\yii\helpers\Url::to(['goods-category/edit','id'=>$goodscategory->id])?>" class="btn btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
                <a href="<?=\yii\helpers\Url::to(['goods-category/delete','id'=>$goodscategory->id])?>" class="btn btn-default"><span class="glyphicon glyphicon-trash"></span></a>
            </td>
        </tr>
    <?php endforeach;?>
</table>

<?php
echo \yii\widgets\LinkPager::widget([
    'pagination'=>$pager,
    //'nextPageLabel'=>'下一页',
    //  'prevPageLabel'=>'上一页',
]);
?>
