<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/13
 * Time: 14:55
 */
namespace backend\models;
use yii\base\Model;

class Login extends Model{
    public $username;
    public $password;
    public $rember;
    public $code;
    public function rules()
    {
        return [
            [['username','password'],'required'],
            ['code','captcha'],
            ['rember','integer']
        ];
    }

    public function attributeLabels()
    {
        return [
          'username'=>'用户名',
            'password'=>'密码',
            'code'=>'验证码',
            'rember'=>'记住我',
        ];
    }

    public function login(){
        $username=Admin::findOne(['username'=>$this->username]);
        if($username){
            //账号存在,验证密码
            if(\Yii::$app->security->validatePassword($this->password,$username->password_hash)){
          //  if($this->password==$username->password_hash){
                //密码正确
                   $username->last_login_time=time();
                   $ip=\Yii::$app->request->userIP;
                   $username->last_login_ip=$ip;
                   $username->save(false);
                   if($this->rember) {
                       return \Yii::$app->user->login($username, 3600 * 24);
                   }else{
                       return \Yii::$app->user->login($username);
                   }
            }else{
                //密码不正确
                $this->addError('password','密码不正确');
            }
        }else{
            //账号不存在
            $this->addError('username','账号不正确');
        }
        return false;
    }

}