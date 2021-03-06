<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $name
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property integer $tel
 * @property integer $delivery_id
 * @property string $delivery_name
 * @property string $delivery_price
 * @property integer $payment_id
 * @property string $payment_name
 * @property string $total
 * @property integer $status
 * @property string $trade_no
 * @property integer $create_time
 */
class Order extends \yii\db\ActiveRecord
{
    public static $delivery=[
        1=>['普通快递送货上门','25','每张订单不满199.00元,运费15.00元, 订单4...'],
        2=>['特快专递','15','每张订单不满299.00元,运费40.00元, 订单4...'],
        3=>['加急快递送货上门','20','每张订单不满399.00元,运费40.00元, 订单4...'],
        4=>['平邮','20','每张订单不满499.00元,运费15.00元, 订单4...'],
    ];
    public static $payment=[
      1=>['货到付款','送货上门后再收款，支持现金、POS机刷卡、支票支付'],
      2=>['在线支付','即时到帐，支持绝大数银行借记卡及部分银行信用卡'],
      3=>['上门自提','自提时付款，支持现金、POS刷卡、支票支付'],
      4=>['邮局汇款','通过快钱平台收款 汇款后1-3个工作日到账'],
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    //商品和购物车关系 多对1
    public function getOrdergoods()
    {
        return $this->hasOne(OrderGoods::className(),['order_id'=>'id']);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'tel', 'delivery_id', 'payment_id', 'status', 'create_time'], 'integer'],
            [['total'], 'number'],
            [['name'], 'string', 'max' => 50],
            [['province', 'city', 'area'], 'string', 'max' => 20],
            [['address', 'delivery_name', 'delivery_price', 'payment_name', 'trade_no'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '用户id',
            'name' => '收货人',
            'province' => '省',
            'city' => '市',
            'area' => '县',
            'address' => '详细地址',
            'tel' => '电话号码',
            'delivery_id' => '配送方式id',
            'delivery_name' => '配送方式名称',
            'delivery_price' => '配送方式价格',
            'payment_id' => '支付方式id',
            'payment_name' => '支付方式名称',
            'total' => '订单金额',
            'status' => '订单状态',
            'trade_no' => '第三方支付交易号',
            'create_time' => '创建时间',
        ];
    }

}
