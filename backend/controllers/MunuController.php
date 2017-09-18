<?php

namespace backend\controllers;

use backend\filters\RbacFilters;
use backend\models\Munu;
use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\data\Pagination;

class MunuController extends \yii\web\Controller
{
    public function actionIndex()
    {
        //1获取所有的用户数据
        //每页多少条,总条数
        //实例化分页工具条
        $pager = new Pagination([
            'totalCount' => Munu::find()->count(),
            'defaultPageSize' => 8,
        ]);
        $munus = Munu::find()->limit($pager->limit)->offset($pager->offset)->orderBy("sort ASC")->all();
        //2 分配数据,调用视图
        return $this->render('index', ['munus' => $munus, 'pager' => $pager]);
    }

    public function actionAdd(){
        $model=new Munu();
        $request=\Yii::$app->request;
        //获取所有的菜单
        $caidan=Munu::find()->where(['=','parent_id','0'])->all();
        //获取路由
        $auth=\Yii::$app->authManager;
        $url=$auth->getPermissions();
      // var_dump($url);die;
        if($request->isPost){
            $model->load($request->post());
            $model->save();
        }
        return $this->render('add',['model'=>$model,'caidan'=>$caidan,'url'=>$url]);
    }

    public function actionEdit($id){
        $model=Munu::findOne(['id'=>$id]);
        $request=\Yii::$app->request;
        //获取所有的菜单
        $caidan=Munu::find()->where(['=','parent_id','0'])->all();
        //获取路由
        $auth=\Yii::$app->authManager;
        //回显路由
      //  $urls=$auth->getPermissions();
        //var_dump($urls);die;
       // $model->url=array_keys($urls);
        $url=$auth->getPermissions();
        // var_dump($url);die;
        if($request->isPost){
            $model->load($request->post());
            $model->save();
        }
        return $this->render('add',['model'=>$model,'caidan'=>$caidan,'url'=>$url]);
    }

    public function actionDelete()
    {
        $id=\Yii::$app->request->post('id');
        $model = Munu::findOne(['id' => $id]);
        if($model) {
            $model->delete();
            return 'success';
            return $this->redirect(['munu/index']);
        }
        return 'fail';
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
