<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods_category`.
 */
class m170910_034523_create_goods_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('goods_category', [
            'id' => $this->primaryKey(),
            'tree' => $this->integer()->notNull(),
            'lft' => $this->integer()->notNull(),
            'rgt' => $this->integer()->notNull(),
            'depth' => $this->integer()->notNull()->comment('深度'),
            'name' => $this->string()->notNull()->comment('商品名称'),
            'parent_id'=>$this->integer()->notNull()->comment('上级分类'),
            'intro'=>$this->text()->notNull()->comment('商品简介'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods_category');
    }
}
