<?php

namespace backend\controllers;

use backend\models\Admin;
use backend\models\Login;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

class AdminController extends \yii\web\Controller
{
    public function actionIndex()
    {
/*        $isGuest=\Yii::$app->user->isGuest;
        if($isGuest){
            return $this->redirect(['admin/login']);
        }*/
    //    var_dump(\Yii::$app->user->isGuest);exit;
        $pager=new Pagination([
            'totalCount'=>Admin::find()->count(),
            'defaultPageSize'=>2,
        ]);
        $admins=Admin::find()->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['admins'=>$admins,'pager'=>$pager]);
    }


    public function actionAdd(){
        $model=new Admin();
        $model->scenario=Admin::SCENARIO_ADD;
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){

                $model->save();
                \Yii::$app->session->setFlash('success', '添加成功');
                $this->redirect(['admin/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    public function actionEdit($id){
        $model=Admin::findOne(['id'=>$id]);
        if($model==null){
            throw new NotFoundHttpException('用户不存在');
        }
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //修改密码
                //确认旧密码是否一致
                // var_dump($psd->password_hash);die;
                if(\Yii::$app->security->validatePassword($model->password,$model->password_hash)){//密码验证正确
                    //验证确认密码
                    if($model->newpassword==$model->repassword) {//两次密码一致
                        $model->save();
                    } else{
                    //    $model->addError('repassword','两次密码不一致');
                        throw new NotFoundHttpException('两次密码不一致');
                    }
                }
                else{//密码验证不正确
                  //  var_dump(1);exit;
                    throw new NotFoundHttpException('密码不正确');
                }

                \Yii::$app->session->setFlash('success', '修改成功');
                $this->redirect(['admin/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        //只能修改自己的信息
        $id=\Yii::$app->user->id;
        if($model->id!=$id){
            throw new NotFoundHttpException('不能修改别人的用户信息');
        }
        $model->password_hash=\Yii::$app->security->passwordHashStrategy;
        return $this->render('edit',['model'=>$model]);
    }

    public function actionDelete(){
        $id=\Yii::$app->request->post('id');
        $admin=Admin::findOne(['id'=>$id]);
        if($admin) {
            $admin->delete();
            return 'success';
            return $this->redirect(['admin/index']);
        }
        return 'fail';
    }

    public function actionLogin(){
        $model=new Login();
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
          //  var_dump($model->rember);die;
            if($model->validate()){
                //认证
                if($model->login()){
                    \Yii::$app->session->setFlash('success','登录成功');
                    return $this->redirect(['admin/index']);
                }
            }
        }
        return $this->render('login',['model'=>$model]);
    }

    public function actionLogout(){
        \Yii::$app->user->logout();
        \Yii::$app->session->setFlash('success','注销成功');
        return $this->redirect(['admin/login']);
    }
    //设置权限
    public function behaviors()
    {
        return [
            'acf'=>[
                'class'=>AccessControl::className(),
                'only'=>['index','delete','add','edit'],
                'rules'=>[
                    [
                        'allow'=>true,
                        'actions'=>['delete','add','edit'],
                        'roles'=>['@'],
                    ],
                    [
                        'allow'=>true,
                        'actions'=>['index'],
                        'roles'=>['?','@'],
                    ],
                ],
            ],
        ];
    }

}
