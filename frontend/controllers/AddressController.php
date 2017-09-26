<?php

namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\Locations;
use yii\web\ForbiddenHttpException;

class AddressController extends \yii\web\Controller
{
    public function actionAddress()
    {
        $model = new Address();
         $request = \Yii::$app->request;
         if ($request->isPost) {
             if(!\Yii::$app->user->isGuest){
              $member_id=\Yii::$app->user->id;
             $model->load($request->post(), '');
                // var_dump($model);die;
                if ($model->validate()) {
                    $model->member_id=$member_id;
                    $model->save();
                    return $this->redirect(['address/address']);
                } else {
                    var_dump($model->getErrors());
                    exit;
                }
            }
             else {
                 throw new ForbiddenHttpException('您还没有登录,请登录!');
             }
         }
        if(!\Yii::$app->user->isGuest) {
            $member_id=\Yii::$app->user->id;
            $addressies = Address::find()->where(['member_id'=> $member_id])->all();
        }else{
            $addressies = [];
        }
        return $this->renderPartial('address', ['model' => $model, 'addressies' => $addressies]);
    }

    public function actionEdit($id)
    {
        $model=Address::findOne(['id'=>$id]);
        $request = \Yii::$app->request;
        if ($request->isPost) {
         //   var_dump($model);die;
            if (!\Yii::$app->user->isGuest) {
                $model->load($request->post(), '');
                if ($model->validate()) {

                    $model->save();
                    return $this->redirect(['address/address']);
                } else {
                    var_dump($model->getErrors());
                    exit;
                }
            }else {
                throw new ForbiddenHttpException('您还没有登录,请登录!');
            }
        }

        return $this->renderPartial('addressedit', ['model' => $model]);
    }

    //删除
    public function actionDelete($id){
        //$id=\Yii::$app->request->post('id');
        $address=Address::findOne(['id'=>$id]);
        if(!\Yii::$app->user->isGuest) {
            if ($address) {
                $address->delete();
                //   return 'success';
                return $this->redirect(['address/address']);
            }
        } else {
            throw new ForbiddenHttpException('您还没有登录,请登录!');
        }
       // return 'fail';
    }


}
