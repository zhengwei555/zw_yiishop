<?php

use yii\db\Migration;

/**
 * Handles the creation of table `admin`.
 */
class m170913_030051_create_admin_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('admin', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique()->comment('用户名'),
            'auth_key' => $this->string(32),
            'password_hash' => $this->string()->notNull()->comment('密码'),
            'password_reset_token' => $this->string()->unique()->notNull()->comment('确认密码'),
            'email' => $this->string()->unique(),

            'status' => $this->smallInteger()->defaultValue(10),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'last_login_time' => $this->integer()->comment('最后登录时间'),
            'last_login_ip' => $this->string(100)->comment('最后登录ip'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('admin');
    }
}
