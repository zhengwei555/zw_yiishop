<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/19
 * Time: 9:22
 */
namespace frontend\models;
use yii\base\Model;
use yii\web\ForbiddenHttpException;

class LoginForm extends Model{
    public $username;
    public $password;
    public $checkcode;
    public $rember;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rember','string'],
            ['checkcode','captcha','message'=>'验证码错误']
        ];
    }
    public function Login()
    {
        $member = Member::findOne(['username' => $this->username]);
        if ($member) {//用户名存在
          //  var_dump(\Yii::$app->security->validatePassword($this->password, $member->password_hash));exit;
            if (\Yii::$app->security->validatePassword($this->password, $member->password_hash)) {//密码正确
                $member->last_login_time = time();
                $ip = \Yii::$app->request->userIP;
                $member->last_login_ip = $ip;
                $member->save(false);
                if($this->rember) {
                    return \Yii::$app->user->login($member, 3600 * 24);
                }else{
                    return \Yii::$app->user->login($member);
                }
            } else {
                //密码不正确
                throw new ForbiddenHttpException('密码不正确');
               // $this->addError('password','密码不正确!');
            }
        } else {
            throw new ForbiddenHttpException('用户名不存在');
            //账号不存在
          //  $this->addError('username','账号不正确');
;
        }
        return false;
    }

}