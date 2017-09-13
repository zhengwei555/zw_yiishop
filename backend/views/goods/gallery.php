<?php
/**
name	varchar(50)	名称
intro	text	简介
article_category_id	int()	文章分类id
sort	int(11)	排序
status	int(2)	状态(-1删除 0隐藏 1正常)
create_time	int(11)	创建时间
 */
use yii\web\JsExpression;
$form=\yii\bootstrap\ActiveForm::begin();
//外部TAG
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
echo \flyok666\uploadifive\Uploadifive::widget([
    'url' => yii\helpers\Url::to(['s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'formData'=>['goods_id' => $goods->id],
        'width' => 120,
        'height' => 40,
        'onError' => new JsExpression(<<<EOF
function(file, errorCode, errorMsg, errorString) {
    console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
}
EOF
        ),
        'onUploadComplete' => new JsExpression(<<<EOF
function(file, data, response) {
    data = JSON.parse(data);
    if (data.error) {
        console.log(data.msg);
    } else {
        console.log(data.fileUrl);
        //将上传文件的路径写入logo字段
        // $("#goods-logo").val(data.fileUrl);
        // $("#img").attr("src",data.fileUrl);
        var html='<tr data-id="'+data.goods_id+'">';
        html += '<td><img src="'+data.fileUrl+'" /></td>';
        html += '<td><button type="button" class="btn btn-danger del_btn">删除</button></td>';
        html += '</tr>';
        $("table").append(html);
    }
}
EOF
        ),
    ]
]);
\yii\bootstrap\ActiveForm::end();
?>
<table class="table">
    <tr>
        <th>图片</th>
        <th>操作</th>
    </tr>
    <?php foreach ($gallerys as $gallery):?>
     <tr data-id="<?=$gallery->id?>">
     <td><?=\yii\bootstrap\Html::img($gallery->path)?></td>
    <td><?=\yii\bootstrap\Html::button('删除',['class'=>'btn btn-danger del_btn'])?></td>
    </tr>
    <?php endforeach;?>
</table>
<?php
$del_url = \yii\helpers\Url::to(['goods/del-gallery']);
$this->registerJs(new JsExpression(
    <<<JS
    $("table").on('click',".del_btn",function(){
        if(confirm("确定删除该图片吗?")){
        var tr=$(this).closest('tr');
           var id=tr.attr('data-id');
            $.post("{$del_url}",{id:id},function(data){
                if(data=="success"){
                    //alert("删除成功");
                    tr.remove();
                }
            });
        }
    });
JS
));


