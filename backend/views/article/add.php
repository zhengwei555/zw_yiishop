<?php
/**
name	varchar(50)	名称
intro	text	简介
article_category_id	int()	文章分类id
sort	int(11)	排序
status	int(2)	状态(-1删除 0隐藏 1正常)
create_time	int(11)	创建时间
 */
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textarea(['rows'=>8]);
echo $form->field($model, 'article_category_id')->dropDownList(\yii\helpers\ArrayHelper::map($articlecategory,'id','name'));
echo $form->field($model,'sort')->textInput();
echo $form->field($model,'status',['inline'=>true])->radioList([0=>'隐藏',1=>'正常']);
echo $form->field($model2,'content')->textarea(['rows'=>8]);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);

\yii\bootstrap\ActiveForm::end();