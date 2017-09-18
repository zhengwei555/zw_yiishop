<?php

namespace backend\controllers;

use backend\filters\RbacFilters;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use backend\models\GoodsSearchForm;
use yii\data\Pagination;
use flyok666\uploadifive\UploadAction;
use flyok666\qiniu\Qiniu;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\filters\AccessControl;
class GoodsController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $query = Goods::find();
        $model=new GoodsSearchForm();
        $model->search($query);
/*        $keyname=\Yii::$app->request->get('keyname');
        $keysn=\Yii::$app->request->get('keysn');
        $minprice=\Yii::$app->request->get('minprice');
        $maxprice=\Yii::$app->request->get('maxprice');
        var_dump($keyname);die;
        if($keyname){
            $query->andWhere(['like','name',$keyname]);
        }
        if($keysn){
            $query->andWhere(['like','sn',$keysn]);
        }
        if($minprice){
            $query->andWhere(['<=','shop_price',$minprice]);
        }
        if($maxprice){
            $query->andWhere(['>=','shop_price',$maxprice]);
        }*/
        //接收表单提交的查询参数

        $pager=new Pagination([
            'totalCount'=>$query->andWhere(['>', 'status', -1])->count(),
            'defaultPageSize'=>2,
        ]);
      //  $model=$query->all();
        $goods=$query->andWhere(['>', 'status', -1])->limit($pager->limit)->offset($pager->offset)->orderBy('sort ASC')->all();
        return $this->render('index',['goods'=>$goods,'pager'=>$pager,'model'=>$model]);
    }

    public function actionAdd(){
        $model=new Goods();
        $GoodsIntro=new GoodsIntro();
        $request=\Yii::$app->request;
        if($request->isPost) {
            $model->load($request->post());
            $GoodsIntro->load($request->post());
            if ($model->validate()&&$GoodsIntro->validate()) {
                $goods_day_count=GoodsDayCount::findOne(['day'=>date("Y-m-d",time())]);
                if($goods_day_count){
                    $goods_day_count->count+=1;//给count+1
                    $model->sn=date("Ymd",time()).sprintf('%04d',$goods_day_count->count);
                }else {
                    $goods_day_count = new GoodsDayCount();
                    $goods_day_count->day = date("Ymd", time());
                    $goods_day_count->count = 1;
                    $model->sn = date("Ymd", time()) . '0001';
                }
                $goods_day_count->save();
                $model->create_time = time();
                $model->save();
                $GoodsIntro->goods_id = $model->id;
                $GoodsIntro->save(false);

                \Yii::$app->session->setFlash('success', '添加商品成功!');
                return $this->redirect(['goods/index']);
            } else {
                //验证失败
                var_dump($model->getErrors());
                exit;
            }
        }
         $brands = Brand::find()->where(['>', 'status', -1])->all();
        return $this->render('add', ['model' => $model, 'brands' => $brands,'GoodsIntro'=>$GoodsIntro]);
    }


    public function actionEdit($id){
        $model=Goods::findOne(['id'=>$id]);
        $GoodsIntro=GoodsIntro::findOne(['goods_id'=>$id]);
        $request=\Yii::$app->request;
        if($request->isPost) {
            $model->load($request->post());
            $GoodsIntro->load($request->post());
            if ($model->validate()&&$GoodsIntro->validate()) {
           //     $model->create_time = time();
                $model->save();
                $GoodsIntro->save(false);

                \Yii::$app->session->setFlash('success', '修改商品成功!');
                return $this->redirect(['goods/index']);
            } else {
                //验证失败
                var_dump($model->getErrors());
                exit;
            }
        }
        $brands = Brand::find()->where(['>', 'status', -1])->all();
        return $this->render('add', ['model' => $model, 'brands' => $brands,'GoodsIntro'=>$GoodsIntro]);
    }

    //删除商品
    public function actionDelete()
    {
        $id=\Yii::$app->request->post('id');
        $model = Goods::findOne(['id' => $id]);
        if($model) {
            $model->status=-1;
            $model->save(false);
            return 'success';
            return $this->redirect(['goods/index']);
        }else {
            return 'fail';
        }
    }

    //详情
    public function actionDetail($id){
        $model = Goods::findOne(['id'=>$id]);
        $GoodsIntro = GoodsIntro::findOne(['goods_id'=>$id]);
        return $this->render('detail', ['model' => $model,'GoodsIntro'=>$GoodsIntro]);
    }

    //商品相册
    public function actionGallery($id){
        $goods=Goods::findOne(['id'=>$id]);
        if($goods==null){
            throw new NotFoundHttpException('该商品不存在');
        }
       $gallerys=GoodsGallery::findAll(['goods_id'=>$id]);
      //  $gallerys=GoodsGallery::find()->all();
        return $this->render('gallery',['goods'=>$goods,'gallerys'=>$gallerys]);
    }

    //删除相册
    public function actionDelGallery(){
        $id=\Yii::$app->request->post('id');
        $model=GoodsGallery::findOne(['id'=>$id]);
        if($model&&$model->delete()){
            return 'success';
        }else{
            return 'fail';
        }
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
                    $model=new GoodsGallery();
                    $model->goods_id=\Yii::$app->request->post('goods_id');
                    $model->path = $action->getWebUrl();
                    $model->save();
                    //返回到页面的路劲
                    $action->output['fileUrl'] = $model->path;
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
