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
    <a href="<?=\yii\helpers\Url::to(['brand/add'])?>"  class="btn btn-default"><span class="glyphicon glyphicon-plus"></span></a>
</div><br>
    <table class="table table-bordered table-responsive table-hover">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>简介</th>
        <th>LOGO</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach ($Brands as $Brand):?>
        <tr data_id="<?=$Brand->id?>">
            <td><?=$Brand->id?></td>
            <td><?=$Brand->name?></td>
            <td><?=$Brand->intro?></td>
            <td><img src="<?=$Brand->logo?>" style="width: 60px"></td>
            <td><?=$Brand->sort?></td>
            <td><?=$Brand->status?'显示':'隐藏'?></td>
            <td><a href="<?=\yii\helpers\Url::to(['brand/edit','id'=>$Brand->id])?>" class="btn btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
                <a href="javascript:; " class="btn btn-default del_btn"><span class="glyphicon glyphicon-trash"></span></a></td>
        </tr>
    <?php endforeach;?>
</table>
