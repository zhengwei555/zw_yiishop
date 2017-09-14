<?php
/* @var $this yii\web\View */

$form = \yii\bootstrap\ActiveForm::begin([
    'method' => 'get',//提交方法
    'action'=>\yii\helpers\Url::to(['goods/index']),//路径
    'options'=>['class'=>'form-inline']//显示为一行
]);
echo $form->field($model,'name')->textInput(['placeholder'=>'商品名'])->label(false);
echo $form->field($model,'sn')->textInput(['placeholder'=>'货号'])->label(false);
echo $form->field($model,'minPrice')->textInput(['placeholder'=>'￥'])->label(false);
echo $form->field($model,'maxPrice')->textInput(['placeholder'=>'￥'])->label('');
echo \yii\bootstrap\Html::submitButton('搜索',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();

?>
    <div>
        <a href="<?= \yii\helpers\Url::to(['goods/add']) ?>" class="btn btn-default"><span
                    class="glyphicon glyphicon-plus"></span></a>
    </div><br>
    <table class="table table-bordered table-responsive table-hover table-striped">
        <tr>
            <th>ID</th>
            <th>商品名称</th>
            <th>商品货号</th>
            <th>LOGO</th>
            <th>商品价格</th>
            <th>库存</th>
            <th>是否在售</th>
            <th>操作</th>
        </tr>
        <?php foreach ($goods as $good): ?>
            <tr data_id="<?= $good->id ?>">
                <td><?= $good->id ?></td>
                <td><?= $good->name ?></td>
                <td><?= $good->sn ?></td>

                <td><img src="<?= $good->logo?>" style="width: 60px"></td>
                <td><?= $good->shop_price ?></td>
                <td><?= $good->stock ?></td>
                <td><?= $good->is_on_sale ? '热销中' : '已停售' ?></td>
                <td>
                    <a href="<?=\yii\helpers\Url::to(['goods/gallery','id'=>$good->id])?>" class="btn btn-default">
                        <span class="glyphicon glyphicon-book"></span></a>
                    <a href="<?= \yii\helpers\Url::to(['goods/detail', 'id' => $good->id]) ?>"
                       class="btn btn-default"><span class="glyphicon glyphicon-eye-open"></span></a>
                    <a href="<?= \yii\helpers\Url::to(['goods/edit', 'id' => $good->id]) ?>"
                       class="btn btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
                    <a href="javascript:; " class="btn btn-default del_btn"><span
                                class="glyphicon glyphicon-trash"></span></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

<?php
echo \yii\widgets\LinkPager::widget([
    'pagination' => $pager,
]);

$del_url = \yii\helpers\Url::to(['goods/delete']);
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
    $('.del_btn').click(function() {
        if(confirm('确定要删除吗?')){
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
