<?php

use yii\db\Migration;

/**
 * Handles the creation of table `munu`.
 */
class m170917_070644_create_munu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('munu', [
            'id' => $this->primaryKey(),
            'name'=>$this->string('50')->notNull()->comment('名称'),
            'parent_id'=>$this->string('50')->notNull()->comment('上级菜单'),
            'url'=>$this->string('50')->notNull()->comment('路由'),
            'sort'=>$this->integer()->notNull()->comment('排序'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('munu');
    }
}
