<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods_day_count`.
 */
class m170910_160516_create_goods_day_count_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('goods_day_count', [
         //   'id' => $this->primaryKey(),
/*            day	date	日期
            count	int	商品数*/
            'day'=>$this->date()->notNull()->comment('添加日期'),
            'count'=>$this->integer()->notNull()->comment('添加商品数'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods_day_count');
    }
}
