<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7
 * Time: 15:35
 */
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'parent_id')->hiddenInput();
//=============ztree==============
echo '    <ul id="treeDemo" class="ztree"></ul>';




//==============ztree==============
echo $form->field($model,'intro')->textarea(['rows'=>5]);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);

\yii\bootstrap\ActiveForm::end();
/**
 * @var $this \yii\web\View
 */

//注册css文件
$this->registerCssFile('@web/ztree/css/zTreeStyle/zTreeStyle.css');
//注册js(需要在js后面加载)
$this->registerJsFile('@web/ztree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);
//注册ztree静态资源
$goodscategories=json_encode(\backend\models\GoodsCategory::getZNodes());
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
	    	        $('#goodscategory-parent_id').val(treeNode.id);
	    	    }
	        }
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var zNodes = {$goodscategories};
        
        zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
            //展开全部节点
            zTreeObj.expandAll(true);
            //修改, 根据当前分类id的parent_id来选中节点
            var node=zTreeObj.getNodeByParam('id',"{$model->parent_id}",null);
            zTreeObj.selectNode();
JS

));