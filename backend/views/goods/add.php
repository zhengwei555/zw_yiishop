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
echo $form->field($model,'name')->textInput();
echo $form->field($model,'logo')->hiddenInput();
//================================upload插件

//外部TAG
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
echo \flyok666\uploadifive\Uploadifive::widget([
    'url' => yii\helpers\Url::to(['s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'formData'=>['someKey' => 'someValue'],
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
        $("#goods-logo").val(data.fileUrl);
        $("#img").attr("src",data.fileUrl);
    }
}
EOF
        ),
    ]
]);
echo \yii\bootstrap\Html::img($model->logo,['id'=>'img','style'=>'width:200px;']);


//============
echo $form->field($model,'goods_category_id')->hiddenInput();
//=============ztree==============

echo '    <ul id="treeDemo" class="ztree"></ul>';

//==============ztree==============
//echo $form->field($model,'brand_id')->textInput();
echo $form->field($model, 'brand_id')->dropDownList(\yii\helpers\ArrayHelper::map($brands,'id','name'));
echo $form->field($model,'market_price')->input('number');
echo $form->field($model,'shop_price')->input('number');
echo $form->field($model,'stock')->textInput();
echo $form->field($model,'is_on_sale',['inline'=>true])->radioList([1=>'在售',0=>'下架']);
echo $form->field($model,'status',['inline'=>true])->radioList([0=>'正常',1=>'回收站']);
echo $form->field($model,'sort')->textInput();
echo $form->field($GoodsIntro,'content')->widget('kucha\ueditor\UEditor',[]);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);

\yii\bootstrap\ActiveForm::end();


//注册css文件
$this->registerCssFile('@web/ztree/css/zTreeStyle/zTreeStyle.css');
//注册js(需要在js后面加载)
$this->registerJsFile('@web/ztree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);
//注册ztree静态资源
$goods=json_encode(\backend\models\Goods::getZNodes());
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
 var zTreeObj;
        // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
        var setting = {
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id",
                    rootPId: 0
                }
            },
            callback: {
	    	onClick:function(event, treeId, treeNode) {
	    	        console.log(treeNode);
	    	        $('#goods-goods_category_id').val(treeNode.id);
	    	    }
	        }
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var zNodes = {$goods};
        
        zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
            //展开全部节点
            zTreeObj.expandAll(true);
            //修改, 根据当前分类id的parent_id来选中节点
            var node=zTreeObj.getNodeByParam('id',"{$model->goods_category_id}",null);
            zTreeObj.selectNode();
JS

));