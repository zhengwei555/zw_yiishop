<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/15
 * Time: 14:08
 */
namespace backend\models;
use yii\base\Model;

class PermissionForm extends Model{
    public $name;//定义权限
    public $des; //定义描述

    const SCENARIO_ADD = 'add';

    public function rules()
    {
        return [
            [['name','des'],'required'],
            ['name','validateName','on'=>self::SCENARIO_ADD],//设置场景
        ];
    }

    //验证权限名称
    public function validateName(){
        if(\Yii::$app->authManager->getPermission($this->name)){
            $this->addError('name','该权限名称已存在!');
        }
    }

    public function attributeLabels()
    {
        return [
            'name'=>'权限名称',
            'des'=>'权限描述',
        ];
    }
}