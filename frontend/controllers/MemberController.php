<?php

namespace frontend\controllers;



use frontend\models\Address;
use frontend\models\LoginForm;
use frontend\models\Member;
use frontend\models\SmsDemo;

class MemberController extends \yii\web\Controller
{

    public function actionRegister(){
        $model=new Member();
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post(),'');

//            var_dump($model->password);die;
            if($model->validate()){
                $model->save();
                $this->redirect(['member/login']);
            } else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->renderPartial('register');
    }

    //ajax验证用户名和手机号码(威胁)唯一
    public function actionValidateMember($username){
        $member=Member::findOne(['username'=>$username]);
        if($member){
            return 'false';
        } else{
            return 'true';
        }
    }
    //ajax验证验证码
    public function actionValidateSms($phone,$sms)
    {
        $redis=new \Redis();
        $redis->connect('127.0.0.1');
        $code = $redis->get('code_' . $phone);
        if ($code == $sms) {
            return 'true';
        }else{
            return 'false';
        }
    }

    public function actionLogin(){
        $model=new LoginForm();
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post(),'');
            if($model->validate()){
                //认证
                if($model->Login()){
                    \Yii::$app->session->setFlash('success','登录成功');
                    return $this->redirect(['shop/index']);
                }
            }else {
                //验证失败
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->renderPartial('login',['model'=>$model]);
    }

    //注销
    public function actionLogout(){
        \Yii::$app->user->logout();
        return $this->redirect(['member/login']);
    }


/*    public function actionTest(){
       var_dump(\Yii::$app->security->generatePasswordHash(123456)) ;
    }*/

    public function actionSms(){
        $phone=\Yii::$app->request->post('phone');
        //判断是否能够发送短信
        //一个手机号码1分钟只能发送一条短信
        $code=rand(1000,9999);
        $redis=new \Redis();
        $redis->connect('127.0.0.1');
        $time=$redis->get('time_'.$phone);
        if($time && (time()-$time<60)){
            echo '不能频繁发送验证码';
            exit;
        }
        $count=$redis->get('count_'.$phone);
        if($count && $count>=20){
            echo '今天发送次数超过20次,不能再发了!';
        }

        $redis->set('code_'.$phone,$code,time()+5*60);
        //保存发送时间
        $redis->set('time_'.$phone,time());
        //保存发送次数
        $redis->set('count_'.$phone,++$count);


/*        $demo = new SmsDemo(
            "LTAIMQ3n1PtzHeCz",
            "o7yABkmgOcEaDFtw5ODJZC5MFmzq48"
        );

        echo "SmsDemo::sendSms\n";
        $response = $demo->sendSms(
            "郑伟的shop", // 短信签名
            "SMS_97940004", // 短信模板编号
            "18200168950", // 短信接收者
            Array(  // 短信模板中字段的值
                "code"=>$code,
                //"product"=>"dsd"
            )
         //   "123"
        );
        print_r($response);*/

        echo $code;
    }

    public function actionRedis(){
        $redis=new \Redis();
        $redis->connect('127.0.0.1');
        $redis->set('name','谢金');
       // var_dump(1);die;
        echo 'ok';
    }

    public function actionPhpinfo(){
        phpinfo();
    }

}
