<?php

namespace frontend\controllers;



use frontend\models\Member;

class MemberController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model=new Member();
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
            }
        }
        return $this->renderPartial('index');
    }

    public function actionRegister(){
        $model=new Member();
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
            }
        }
        return $this->renderPartial('index');
    }

}
