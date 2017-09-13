<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article_category`.
 */
class m170907_103154_create_article_category_table extends Migration
{
    /**
     * @inheritdoc
     */
/*name	varchar(50)	名称
intro	text	简介
sort	int(11)	排序
status	int(2)	状态(-1删除 0隐藏 1正常)*/
    public function up()
    {
        $this->createTable('article_category', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(50)->comment('文章名称'),
            'intro'=>$this->text()->comment('文章简介'),
            'sort'=>$this->integer(11)->comment('文章排序'),
            'status'=>$this->smallInteger()->comment('文章状态'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article_category');
    }
}
