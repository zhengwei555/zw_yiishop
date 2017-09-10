<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/10
 * Time: 22:10
 */
$form=\yii\bootstrap\ActiveForm::begin();

echo \yii\bootstrap\Html::submitButton('返回',['class'=>'btn btn-info']);
echo $form->field($model,'name')->textInput();
//echo $form->field($model, 'article_category_id')->dropDownList(\yii\helpers\ArrayHelper::map($articlecategory,'id','name'));
echo $form->field($model2,'content')->textarea(['rows'=>8]);


\yii\bootstrap\ActiveForm::end();