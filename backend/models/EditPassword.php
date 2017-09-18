<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/15
 * Time: 11:53
 */
namespace backend\models;
use yii\base\Model;

class EditPassword extends Model{
    public $password;
    public $newpassword;
    public $repassword;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password','newpassword','repassword'],'required'],
            ['repassword','compare','compareAttribute'=>'newpassword','message'=>'两次密码不一致!'],//两次密码一致
            ['password','validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => '旧密码',
            'newpassword' => '新密码',
            'repassword' => '确认密码',
        ];
    }
    public function validatePassword(){
        //验证密码
        $id=\Yii::$app->user->id;
        $model=Admin::findOne(['id'=>$id]);
        if(!\Yii::$app->security->validatePassword($this->password,$model->password_hash)){
            $this->addError('password','旧密码不正确');
        }
    }
}