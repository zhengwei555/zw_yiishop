<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/13
* Time: 18:11
*/
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput();
echo $form->field($model,'password')->passwordInput();
echo $form->field($model,'code')->widget(\yii\captcha\Captcha::className());
echo $form->field($model,'rember')->checkbox(['记住我']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();