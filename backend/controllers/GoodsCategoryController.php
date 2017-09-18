<?php

namespace backend\controllers;

use backend\filters\RbacFilters;
use backend\models\ArticleCategory;
use backend\models\GoodsCategory;
use yii\data\Pagination;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
class GoodsCategoryController extends \yii\web\Controller
{

    public function actionIndex()
    {
/*        $isGuest=\Yii::$app->user->isGuest;
        // var_dump($isGuest);die;
        if($isGuest) {
            return $this->redirect(['admin/login']);
            exit;
        }*/
        //1获取所有的用户数据
        //$model = new GoodsCategory();
        //每页多少条,总条数
        //实例化分页工具条
        $pager = new Pagination([
            'totalCount' => GoodsCategory::find()->count(),
            'defaultPageSize' => 2,
        ]);
        $goodscategorys = GoodsCategory::find()->limit($pager->limit)->offset($pager->offset)->all();
        //2 分配数据,调用视图
        return $this->render('index', ['goodscategorys' => $goodscategorys, 'pager' => $pager]);

    }

    public function actionAdd()
    {
        $model = new GoodsCategory();
        $requset = \Yii::$app->request;
        if ($requset->isPost) {
            //接收数据
            $model->load($requset->post());
            if ($model->validate()) {
                if ($model->parent_id) {
                    //非顶级分类
                    $parent = GoodsCategory::findOne(['id' => $model->parent_id]);
                    $model->prependTo($parent);
                } else {
                    //顶级分类
                    $model->makeRoot();
                }
                \Yii::$app->session->setFlash('success', '商品添加成功!');
                return $this->redirect(['goods-category/index']);
            } else {
                //验证失败
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add', ['model' => $model]);
    }

    //修改
    public function actionEdit($id)
    {
        $model = GoodsCategory::findOne(['id' => $id]);
        $requset = \Yii::$app->request;
        if ($requset->isPost) {
            //接收数据
            $model->load($requset->post());
            if ($model->validate()) {
                if ($model->parent_id) {
                    //非顶级分类
                    $parent = GoodsCategory::findOne(['id' => $model->parent_id]);
                    $model->prependTo($parent);
                } else {
                    //顶级分类
                    //修改顶级分类,不改变层级
                    if($model->getOldAttribute('parent_id')==0){
                        $model->save();
                    }else {
                        $model->makeRoot();
                    }
                }
                \Yii::$app->session->setFlash('success', '商品修改成功!');
                return $this->redirect(['goods-category/index']);
            } else {
                //验证失败
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add', ['model' => $model]);
    }

    //ztree测试
    public function actionZtree()
    {
        $goodscategories = GoodsCategory::find()->select(['id', 'parent_id', 'name'])->asArray()->all();
        //   var_dump($goodscategories);
        return $this->renderPartial('ztree');
    }

    //删除
    public function actionDelete()
    {
        $id=\Yii::$app->request->post('id');
        $model = GoodsCategory::findOne(['id' => $id]);
        $child = GoodsCategory::findOne(['parent_id' => $model->id]);
            //方法2
/*            if($model->isleaf()){
                $model->deleteWithChildren();
            } else {
               \Yii::$app->session->setFlash('success', '有子分类不能删除!');

            }*/
            if ($child) {
                \Yii::$app->session->setFlash('success', '有子分类不能删除!');
            } else {
                $model->deleteWithChildren();
            }


        return $this->redirect(['goods-category/index']);
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
