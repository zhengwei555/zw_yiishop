<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use flyok666\uploadifive\UploadAction;
use flyok666\qiniu\Qiniu;
use yii\filters\AccessControl;
class BrandController extends \yii\web\Controller
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
            'totalCount' => Brand::find()->where(['>', 'status', '-1'])->count(),
            'defaultPageSize' => 2,
        ]);
        $Brands = Brand::find()->where(['>', 'status', '-1'])->limit($pager->limit)->offset($pager->offset)->orderBy("sort ASC")->all();
        //2 分配数据,调用视图
        return $this->render('index', ['Brands' => $Brands, 'pager' => $pager]);
    }

    public function actionAdd()
    {
        //实例化模型对象
        $model = new Brand();
        //实例化请求参数
        $request = \Yii::$app->request;
        if ($request->isPost) {
            //接收数据
            $model->load($request->post());
            //验证规则
            if ($model->validate()) {
                $model->save(false);
                \Yii::$app->session->setFlash('success', '添加成功');
                return $this->redirect(['brand/index']);
            } else {
                //验证失败
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add', ['model' => $model]);
    }

    //修改商品
    public function actionEdit($id)
    {
        //实例化模型对象
        $model = Brand::findOne(['id' => $id]);
        //实例化请求参数
        $request = \Yii::$app->request;
        if ($request->isPost) {
            //接收数据
            $model->load($request->post());
            //验证规则
            if ($model->validate()) {
                $model->save(false);
                \Yii::$app->session->setFlash('success', '修改成功');
                return $this->redirect(['brand/index']);
            } else {
                //验证失败
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add', ['model' => $model]);
    }

    //删除商品
    public function actionDelete()
    {
        $id=\Yii::$app->request->post('id');
        $model = Brand::findOne(['id' => $id]);
        if($model) {
            $model->status = -1;
            $model->save(false);
            return 'success';
            return $this->redirect(['brand/index']);
        }
        return 'fail';
    }

    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
               // 'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
/*                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filename = sha1_file($action->uploadfile->tempName);
                    return "{$filename}.{$fileext}";
                },*/
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    //  $action->output['fileUrl'] = $action->getWebUrl();
                 //   $action->getFilename(); // "image/yyyymmddtimerand.jpg"
                 //  $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
                 //   $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                //将图片上传七里云,并返回七里云地址
/*                    $config = [
                        'accessKey'=>'_Fj7hAZt_q8vrMbtIhCQHj5r5DhVrF_2Lkh-77ot',
                        'secretKey'=>'sJk7ct2iw2vcLj_zE0t80XZfKFqSRwE2QrV5S1WD',
                        'domain'=>'http://ovyfqayrd.bkt.clouddn.com/',
                        'bucket'=>'zhengwei',
                        'area'=>Qiniu::AREA_HUADONG  //华东
                    ];*/
                    $qiniu = new Qiniu(\Yii::$app->params['qiniuyun']);
                    $key = $action->getWebUrl();
                    $file=$action->getSavePath();
                    $qiniu->uploadFile($file,$key);
                    $url = $qiniu->getLink($key);
                    $action->output['fileUrl']=$url;//输出图片路径
                },
            ],
        ];
    }
/*    //测试七牛云
    public function actionQiniu(){
        $config = [
            'accessKey'=>'_Fj7hAZt_q8vrMbtIhCQHj5r5DhVrF_2Lkh-77ot',
            'secretKey'=>'sJk7ct2iw2vcLj_zE0t80XZfKFqSRwE2QrV5S1WD',
            'domain'=>'http://ovyfqayrd.bkt.clouddn.com/',
            'bucket'=>'zhengwei',
            'area'=>Qiniu::AREA_HUADONG  //华东
        ];
        $qiniu = new Qiniu($config);
        $key = '1.jpg';
        $file=\Yii::getAlias('@webroot/upload/1.jpg');
        $qiniu->uploadFile($file,$key);
        $url = $qiniu->getLink($key);
        var_dump($url);
    }*/
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
