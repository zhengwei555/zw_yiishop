<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use yii\data\Pagination;
use yii\filters\AccessControl;
class ArticleController extends \yii\web\Controller
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
            'totalCount' => Article::find()->where(['>','status','-1'])->count(),
            'defaultPageSize' => 2,
        ]);
        $articles = Article::find()->where(['>','status','-1'])->limit($pager->limit)->offset($pager->offset)->orderBy('sort ASC')->all();
        //2 分配数据,调用视图
        return $this->render('index', ['articles' => $articles, 'pager' => $pager]);
    }

//添加文章
    public function actionAdd()
    {
        $model = new Article();
        $model2= new ArticleDetail();
        $requset = \Yii::$app->request;
        if ($requset->isPost) {
            //接收数据
            $model->load($requset->post());
            $model2->load($requset->post());
            if ($model->validate()&&$model2->validate()) {
                $model->create_time=time();
                $model->save(false);
                $model2->article_id=$model->id;
                $model2->save(false);
                \Yii::$app->session->setFlash('success', '文章添加成功!');
                return $this->redirect(['article/index']);
            } else {
                //验证失败
                var_dump($model->getErrors());
                exit;
            }
        }
        $articlecategory=ArticleCategory::find()->where(['>','status',-1])->all();
        return $this->render('add', ['model' => $model,'articlecategory'=>$articlecategory,'model2'=>$model2]);
    }
//修改文章
    public function actionEdit($id)
    {
        $model = Article::findOne(['id'=>$id]);
        $model2 = ArticleDetail::findOne(['article_id'=>$id]);
        $requset = \Yii::$app->request;
        if ($requset->isPost) {
            //接收数据
            $model->load($requset->post());
            $model2->load($requset->post());
            if ($model->validate()&&$model2->validate()) {
                $model->save(false);
                $model2->article_id=$model->id;
                $model2->save(false);
                \Yii::$app->session->setFlash('success', '文章修改成功!');
                return $this->redirect(['article/index']);
            } else {
                //验证失败
                var_dump($model->getErrors());
                exit;
            }
        }
        $articlecategory=ArticleCategory::find()->where(['>','status',-1])->all();
        return $this->render('add', ['model' => $model,'articlecategory'=>$articlecategory,'model2'=>$model2]);
    }
    //删除作者
    public function actionDelete(){
        $id=\Yii::$app->request->post('id');
        $model = Article::findOne(['id' => $id]);
        if($model) {
        $model->status = -1;
        $model->save(false);
            return 'success';
            return $this->redirect(['article/index']);
        }
        return 'fail';
    }
    public function actionDetail($id){
        $model = Article::findOne(['id'=>$id]);
        $model2 = ArticleDetail::findOne(['article_id'=>$id]);
        $articlecategory=ArticleCategory::findOne(['id'=>$id]);
        return $this->render('detail', ['model' => $model,'articlecategory'=>$articlecategory,'model2'=>$model2]);
    }

    public function actions()
    {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
            ]
        ];
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
