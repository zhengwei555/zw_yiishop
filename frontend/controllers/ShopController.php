<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/20
 * Time: 19:24
 */
namespace frontend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\data\Pagination;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\ForbiddenHttpException;

class ShopController extends Controller
{
    public $enableCsrfValidation=false;
    public function actionIndex()
    {

        $goods = GoodsCategory::find()->where(['parent_id' => 0])->all();
        return $this->renderPartial('index', ['goods' => $goods]);
    }

    public function actionList($goods_id)
    {

        $good = GoodsCategory::findOne(['id' => $goods_id]);
        $query = Goods::find();
        if ($good->depth == 2) {  //3及分类
            $query->andWhere(['goods_category_id' => $goods_id]);
        } else {
            $ids = $good->children()->select('id')->andWhere(['depth' => 2])->column();
            $query->andWhere(['in', 'goods_category_id', $ids]);
        }
        $pager = new Pagination([
            'totalCount' => $query->count(),
            'defaultPageSize' => 2,
        ]);

        $lists = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->renderPartial('list', ['lists' => $lists, 'pager' => $pager]);
    }

    //商品详情
    public function actionDetail($id)
    {
        $GoodsCategory = GoodsCategory::findOne(['id' => $id]);
        $goods = Goods::findOne(['id' => $id]);
        $details = GoodsIntro::findOne(['goods_id' => $id]);
        $imgs = GoodsGallery::find()->where(['goods_id' => $id])->all();
        // var_dump($imgs);die;
        return $this->renderPartial('goods', ['details' => $details, 'goods' => $goods, 'GoodsCategory' => $GoodsCategory, 'imgs' => $imgs]);
    }

    //加入购物车
    public function actionAddcart($goods_id, $amount)
    {
        //未登录
        if (\Yii::$app->user->isGuest) {
            //存在cookie
            $cookies = \Yii::$app->request->cookies;
            $val = $cookies->getValue('carts');
            if ($val) {
                $carts = unserialize($val);
            } else {
                $carts = [];
            }
            //检查是否存在商品
            if (array_key_exists($goods_id, $carts)) {
                $carts[$goods_id] += $amount;
            } else {
                $carts[$goods_id] = intval($amount);
            }
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie();
            $cookie->name = 'carts';
            $cookie->value = serialize($carts);
            $cookie->expire = time() + 7 * 24 * 3600;//过期时间
            $cookies->add($cookie);
            // var_dump($goods_id);die;
        } else {  //存数据库

            //检查是否存在商品
                $member_id=\Yii::$app->user->id;
                $value=Cart::findOne(['goods_id'=>$goods_id,'member_id'=>$member_id]);
                if ($value) {
                    $value->amount += $amount;
                    $value->save();
                } else {
                    $model = new Cart();
                    $model->amount = $amount;
                    $model->goods_id=$goods_id;
                    $model->member_id=\Yii::$app->user->id;
                    $model->save();
                }
        }
        return $this->redirect(['shop/cart']);
    }

    public function actionCart()
    {
        //获取购物车数据
        if (\Yii::$app->user->isGuest) { //未登录
            $cookies = \Yii::$app->request->cookies;
            $value = $cookies->getValue('carts');
            if ($value) {
                $carts = unserialize($value);
            } else {
                $carts = [];
            }
            $goods = Goods::find()->where(['in', 'id', array_keys($carts)])->all();
        } else {
            //登录后同步
            $request = \Yii::$app->request;
            $cookies = $request->cookies;
            $value = $cookies->getValue('carts');
            // var_dump($value);die;
            if ($value) {
                $carts = unserialize($value);
                // var_dump($carts);die;
                //遍历数据
                foreach ($carts as $goods_id => $amount) {
                    $member_id = \Yii::$app->user->id;
                    $value = Cart::findOne(['goods_id' => $goods_id, 'member_id' => $member_id]);
                    if ($value) {
                        $value->amount += $amount;
                        $value->save();
                    } else {
                        $model = new Cart();
                        $model->amount = $amount;
                        $model->goods_id = $goods_id;
                        $model->member_id = \Yii::$app->user->id;
                        $model->save();
                    }
                }
                //清除cookie
                if (\Yii::$app->request->cookies->get('carts')) {
                    \yii::$app->response->cookies->remove('carts');
                }
            }else{
                $member_id = \Yii::$app->user->id;
                $gods = Cart::find()->where(['member_id'=>$member_id])->all();
                $carts = [];
                foreach ($gods as $good){
                $carts[$good->goods_id] = $good->amount;
                }
            }
            $goods = Goods::find()->where(['in', 'id', array_keys($carts)])->all();
        }
      //  var_dump(array_values($carts)[1]);die;
        return $this->renderPartial('cart', ['goods' => $goods, 'carts' => $carts]);
    }


    //ajax添加减少
    public function actionAjax(){
        $request=\Yii::$app->request;
        $goods_id=$request->post('goods_id');
        $amount=$request->post('amount');
        $member_id = \Yii::$app->user->id;
        if(\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            $value = $cookies->getValue('carts');
            if($value){
                $carts = unserialize($value);
            }else{
                $carts = [];
            }
            //检查是否存在商品
            if(array_key_exists($goods_id,$carts)){
                $carts[$goods_id] = $amount;
            }
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie();
            $cookie->name = 'carts';
            $cookie->value = serialize($carts);
            $cookie->expire = time()+7*24*3600;//过期时间
            $cookies->add($cookie);
        }else{//登录修改保存数据库
            $cart=Cart::findOne(['goods_id'=>$goods_id,'member_id'=>$member_id]);
            $cart->amount=$amount;
            $cart->save();
        }
    }

    //删除
    public function actionDelete()
    {
        $id=\Yii::$app->request->post('goods_id');
        $model = Cart::findOne(['goods_id' => $id]);
        if($model) {
            $model->delete();
            return 'success';
            return $this->redirect(['shop/cart']);
        }
        return 'fail';
    }

    //订单
    public function actionOrder(){
        if(!\Yii::$app->user->isGuest){//已登录
            //1  显示订单表单
           // var_dump($count);die;
            $member_id=\Yii::$app->user->id;
            $addressies=Address::find()->where(['member_id'=>$member_id])->all();
            $carts=Cart::find()->where(['member_id'=>$member_id])->all();
            $deliverys=order::$delivery;
            $payments=order::$payment;

            //2 提交表单
            $request=\Yii::$app->request;
            if($request->isPost){
                $order=new Order();
                //接收数据
              //  $order->load($request->post());
                $model=$request->post();
              //  var_dump($model);die;
                $address=Address::findOne(['id'=>$model['address_id'],'member_id'=>$member_id]);
                $order->member_id=$address->member_id;
                $order->name=$address->name;
                $order->province=$address->province;
                $order->city=$address->city;
                $order->tel=$address->tel;
                $order->area=$address->area;
                $order->address=$address->area_tail;

                $order->delivery_id=$model['delivery'];
                $order->delivery_name=$deliverys[$model['delivery']][0];
                $order->delivery_price=$deliverys[$model['delivery']][1];

                $order->payment_id=$model['pay'];
               // var_dump($model['pay']);
                $order->payment_name=$payments[$model['pay']][0];
                $order->create_time=time();

                //计算价格
                $shiwu=\Yii::$app->db->beginTransaction();//开启失误
                try{
                    $order->save();

                    //订单商品详情表
                    foreach ($carts as $cart){
                        //检查库存
                        if($cart->amount>$cart->goods->stock){
                            //库存不足,无法下单
                            throw new Exception($cart->goods->name.'库存不足,无法下单');
                        }
                        $order_goods=new OrderGoods();
                        $order_goods->order_id=$order->id;
                        $order_goods->goods_id=$cart->goods_id;
                        $order_goods->goods_name=$cart->goods->name;
                        $order_goods->amount=$cart->amount;
                        $order_goods->price=$cart->goods->shop_price;
                        $order_goods->logo=$cart->goods->logo;
                        $order_goods->total=1;
                        //   var_dump($order_goods);die;
                        $order_goods->save();
                        //减库存
                        $goods=Goods::findOne(['id'=>$cart->goods_id]);
                        $goods->stock=$goods->stock-$cart->amount;
                        $goods->save();



                        //清除购物车
                        $cart->delete();
                        //提交事务
                        $shiwu->commit();
                        return $this->redirect(['shop/flow3']);
                    }
                }catch (Exception $e){
                    //回滚
                    $shiwu->rollBack();
                }
            }
            return $this->renderPartial('flow2',['addressies'=>$addressies,'deliverys'=>$deliverys,
                'payments'=>$payments,'carts'=>$carts]);
        }

        else{  //未登录,调到登录页面
            return $this->redirect(['member/login']);
        }
    }


    public function actionFlow3(){

        return $this->renderPartial('flow3');
    }

    //订单表
    public function actionOrders(){
        $member_id=\Yii::$app->user->id;
        $models=Order::find()->where(['member_id'=>$member_id])->all();
       // var_dump(1);die;
        return $this->renderPartial('order',['models'=>$models]);
    }


    //删除的订单表
    public function actionDeleteOrder()
    {
        $id=\Yii::$app->request->post('goods_id');
        $model = OrderGoods::findOne(['goods_id' => $id]);
        if($model) {
            $model->delete();
            return 'success';
            return $this->redirect(['shop/cart']);
        }
        return 'fail';
    }
}