<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order`.
 */
class m170922_084903_create_order_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('order', [
            'id' => $this->primaryKey(),
            'member_id'=>$this->integer()->comment('用户id'),
            'name'=>$this->string(50)->comment('收货人'),
            'province'=>$this->string(20)->comment('省'),
            'city'=>$this->string(20)->comment('市'),
            'area'=>$this->string(20)->comment('县'),
            'address'=>$this->string(255)->comment('详细地址'),
            'tel'=>$this->integer(11)->comment('电话号码'),
            'delivery_id'=>$this->integer()->comment('配送方式id'),
            'delivery_name'=>$this->string()->comment('配送方式名称'),
            'delivery_price'=>$this->string()->comment('配送方式价格'),
            'payment_id'=>$this->integer()->comment('支付方式id'),
            'payment_name'=>$this->string()->comment('支付方式名称'),
            'total'=>$this->decimal(10,2)->comment('订单金额'),
            'status'=>$this->integer()->comment('订单状态'),
            'trade_no'=>$this->string()->comment('第三方支付交易号'),
            'create_time'=>$this->integer()->comment('创建时间'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('order');
    }
}
