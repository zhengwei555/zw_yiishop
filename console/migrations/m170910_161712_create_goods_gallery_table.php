<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods_gallery`.
 */
class m170910_161712_create_goods_gallery_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('goods_gallery', [
            'id' => $this->primaryKey(),
            'goods_id' => $this->integer()->notNull()->comment('商品id'),
            'path' => $this->string()->notNull()->comment('图片地址'),
/*            goods_id	int	商品id
        path	varchar(255)	图片地址*/
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods_gallery');
    }
}
