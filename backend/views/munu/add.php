<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/17
 * Time: 14:32
 */

$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'parent_id')->dropDownList(\yii\helpers\ArrayHelper::map($caidan,'id','name'),['prompt'=>'--请选择上级菜单--']);
echo $form->field($model,'url')->dropDownList(\yii\helpers\ArrayHelper::map($url,'name','name'),['prompt'=>'--请选择路由--']);
echo $form->field($model,'sort')->textInput();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();