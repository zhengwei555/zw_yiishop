<?php

namespace backend\controllers;

use backend\models\ArticleCategory;
use yii\data\Pagination;
use yii\filters\AccessControl;
class ArticleCategoryController extends \yii\web\Controller
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
        //每页多少条,总条数
        //实例化分页工具条
        $pager = new Pagination([
            'totalCount' => ArticleCategory::find()->where(['>','status','-1'])->count(),
            'defaultPageSize' => 2,
        ]);
        $articlecategorys = ArticleCategory::find()->where(['>','status','-1'])->limit($pager->limit)->offset($pager->offset)->orderBy('sort ASC')->all();
        //2 分配数据,调用视图
        return $this->render('index', ['articlecategorys' => $articlecategorys, 'pager' => $pager]);
    }

//添加文章
    public function actionAdd()
    {
        $model = new ArticleCategory();
        $requset = \Yii::$app->request;
        if ($requset->isPost) {
            //接收数据
            $model->load($requset->post());
            if ($model->validate()) {
                $model->save(false);
                \Yii::$app->session->setFlash('success', '文章添加成功!');
                return $this->redirect(['article-category/index']);
            } else {
                //验证失败
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add', ['model' => $model]);
    }

//修改文章
    public function actionEdit($id)
    {
        $model = ArticleCategory::findOne(['id' => $id]);
        $requset = \Yii::$app->request;
        if ($requset->isPost) {
            //接收数据
            $model->load($requset->post());
            if ($model->validate()) {
                $model->save(false);
                \Yii::$app->session->setFlash('success', '文章修改成功!');
                return $this->redirect(['article-category/index']);
            } else {
                //验证失败
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add', ['model' => $model]);
    }

    //删除作者
    public function actionDelete(){
        $id=\Yii::$app->request->post('id');
        $model = ArticleCategory::findOne(['id' => $id]);
        if($model) {
            $model->status = -1;
            $model->save();
            return 'success';
            return $this->redirect(['article-category/index']);
        }
        return 'fail';
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
