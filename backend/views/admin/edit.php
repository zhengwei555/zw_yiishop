<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/13
 * Time: 11:39
 */
$form=\yii\bootstrap\ActiveForm::begin();

echo $form->field($model,'password')->passwordInput();
echo $form->field($model,'newpassword')->passwordInput();
echo $form->field($model,'repassword')->passwordInput();

echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();