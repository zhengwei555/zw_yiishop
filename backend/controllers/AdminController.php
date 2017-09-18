<?php

namespace backend\controllers;

use backend\filters\RbacFilters;
use backend\models\Admin;
use backend\models\EditPassword;
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
                //给用户分配角色
                $model->save();     //保存数据
                $id=\Yii::$app->db->getLastInsertID();  //获取保存数据的id
                $auth=\Yii::$app->authManager;
            //    $admin=$auth->createRole($model->username);
                if($model->roles){
                    foreach ($model->roles as $roleName){
                        $role=$auth->getRole($roleName);
                        $auth->assign($role,$id);  //给用户分配角色
                    }
                }


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
        $request=\Yii::$app->request;
        $auth=\Yii::$app->authManager;
        //获取当前用户关联的角色   兼职对
        $role=$auth->getRolesByUser($id);
       // var_dump($role);die;
        //回显数据
        $model->roles=array_keys($role);//只需要键名
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save(false);
                //给用户分配角色
                //先清除所有角色
                //var_dump(1);die;
                $auth->revokeAll($id);
                if($model->roles){
                    foreach ($model->roles as $roleName){
                        $role=$auth->getRole($roleName);
                        $auth->assign($role,$id);
                    }
                }
                \Yii::$app->session->setFlash('success', '修改成功');
                $this->redirect(['admin/index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    public function actionEditpsd(){
        $model=new EditPassword();
        if($model==null){
            throw new NotFoundHttpException('用户不存在');
        }
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $admin = \Yii::$app->user->identity;
                $admin->password_hash=\Yii::$app->security->generatePasswordHash($model->newpassword);
                 $admin->save();

                \Yii::$app->session->setFlash('success', '修改成功');
                $this->redirect(['admin/index']);
            }
        }
        //只能修改自己的信息
/*        $id=\Yii::$app->user->id;
        if($model->id!=$id){
            throw new NotFoundHttpException('不能修改别人的用户信息');
        }*/
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
            'rbac'=>[
                'class'=>RbacFilters::className(),
                'except'=>['login','logout','error','captcha','editpsd'],
            ]
        ];
    }

}
