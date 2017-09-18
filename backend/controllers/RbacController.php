<?php

namespace backend\controllers;

use backend\filters\RbacFilters;
use backend\models\PermissionForm;
use backend\models\RoleForm;

class RbacController extends \yii\web\Controller
{
    //添加权限
    public function actionAdd(){
        $model=new PermissionForm();
        $model->scenario=PermissionForm::SCENARIO_ADD;  //使用场景
        //实例化请求参数
        $request = \Yii::$app->request;
        if ($request->isPost) {
            //接收数据
            $model->load($request->post());
            //验证规则
            if ($model->validate()) {
                $auth=\Yii::$app->authManager;
                //添加权限
                 //1 创建权限
                $permission=$auth->createPermission($model->name);
                $permission->description=$model->des;
                // 2 保存权限
                $auth->add($permission);
                \Yii::$app->session->setFlash('success', '添加成功');
                return $this->redirect(['rbac/index']);
            }
        }

        return $this->render('add',['model'=>$model]);
    }

    public function actionIndex()
    {
        $auth=\Yii::$app->authManager;
        $permissions=$auth->getPermissions();

        return $this->render('index',['permissions'=>$permissions]);
    }

    //修改权限
    public function actionEdit($name){
        $model=new PermissionForm();
        $auth=\Yii::$app->authManager;
        //获得要修改的权限名称
        $permission=$auth->getPermission($name);
        //回显修改数据
        $model->name=$permission->name;
        $model->des=$permission->description;
        //实例化请求参数
        $request = \Yii::$app->request;
        if ($request->isPost) {
            //接收数据
            $model->load($request->post());
            //验证规则
            if ($model->validate()) {
                //1 创建权限
                if($permission->name==$model->name) {//不修改权限名
                    //修改权限
                    $permission->description = $model->des;
               //     $permission->name = $model->name;
                    // 2 保存修改的权限
                    $auth->update($permission->name, $permission);
                    \Yii::$app->session->setFlash('success', '修改成功');
                    return $this->redirect(['rbac/index']);
                }else{//修改权限名
                    if(!\Yii::$app->authManager->getPermission($model->name)){//数据库不存在该权限
                        //修改权限
                        $permission->description = $model->des;
                        $permission->name = $model->name;
                        // 2 保存修改的权限
                        $auth->update($name, $permission);
                        \Yii::$app->session->setFlash('success', '修改成功');
                        return $this->redirect(['rbac/index']);
                    }else{
                        $model->addError('name','该权限名称已存在!');
                    }
                }
            }
        }

        return $this->render('add',['model'=>$model]);
    }

    public function actionDelete($name){
        $auth=\Yii::$app->authManager;
        //获得要修改的权限名称
        $permission=$auth->getPermission($name);
        $auth->remove($permission);
        \Yii::$app->session->setFlash('success', '删除成功');
        return $this->redirect(['rbac/index']);
    }

    //添加角色
    public function actionAddRole(){
        $model=new RoleForm();
        $model->scenario=RoleForm::SCENARIO_ADD;
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //验证通过后保存角色
                $auth=\Yii::$app->authManager;
                $role=$auth->createRole($model->name);
                $role->description=$model->des;
                //保存到数据表
                $auth->add($role);
                //给角色分配权限
                if($model->permissions){
                    foreach ($model->permissions as $permissionName){
                        $permission=$auth->getPermission($permissionName);
                        $auth->addChild($role,$permission);
                    }
                }
                return $this->redirect(['role-index']);
            }
        }
        return $this->render('role',['model'=>$model]);
    }

    public function actionRoleIndex(){
        $auth=\Yii::$app->authManager;
        $roles=$auth->getRoles();
        return $this->render('role-index',['roles'=>$roles]);
    }

    public function actionEditRole($name){
        $model=new RoleForm();
        $auth=\Yii::$app->authManager;
        //获得要修改的角色名称
        $role=$auth->getRole($name);
        //获取当前角色关联的权限   兼职对
        $permissions=$auth->getPermissionsByRole($name);
        //回显修改数据
        $model->name=$role->name;
        $model->des=$role->description;
        $model->permissions=array_keys($permissions);//只需要键名
        //实例化请求参数
        $request = \Yii::$app->request;
        if ($request->isPost) {
            //接收数据
            $model->load($request->post());
            //验证规则
            if ($model->validate()) {
                //1 创建权限
                if($role->name==$model->name) {     //不修改权限名
                    //修改权限
                    $role->description = $model->des;
                    //     $permission->name = $model->name;
                    // 2 保存修改的权限
                    $auth->update($role->name, $role);
                    //给角色分配权限
                    //先清除所有权限
                    $auth->removeChildren($role);
                    if($model->permissions){
                        foreach ($model->permissions as $permissionName){
                            $permission=$auth->getPermission($permissionName);
                            $auth->addChild($role,$permission);
                        }
                    }
                    \Yii::$app->session->setFlash('success', '修改成功');
                    return $this->redirect(['rbac/role-index']);
                }else{          //修改权限名
                    if(!\Yii::$app->authManager->getRole($model->name)){//数据库不存在该权限
                        //修改权限
                        $role->description = $model->des;
                        $role->name = $model->name;
                        // 2 保存修改的权限
                        $auth->update($name, $role);
                        $auth->removeChildren($role);
                        //给角色分配权限
                        if($model->permissions){
                            foreach ($model->permissions as $permissionName){
                                $permission=$auth->getPermission($permissionName);
                                $auth->addChild($role,$permission);
                            }
                        }
                        \Yii::$app->session->setFlash('success', '修改成功');
                        return $this->redirect(['rbac/role-index']);
                    }else{
                        $model->addError('name','该角色名称已存在!');
                    }
                }
            }
        }

        return $this->render('role',['model'=>$model]);
    }

    public function actionDeleteRole($name){
        $auth=\Yii::$app->authManager;
        $role=$auth->getRole($name);
        $auth->remove($role);
        \Yii::$app->session->setFlash('success', '删除成功');
        return $this->redirect(['rbac/role-index']);
    }

    //设置权限
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilters::className(),
                'except'=>['login','logout','error','captcha'],
            ]
        ];
    }
}
