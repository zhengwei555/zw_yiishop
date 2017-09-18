<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/15
 * Time: 18:59
 */
namespace backend\models;
use yii\base\Model;

class RoleForm extends Model{
    public $name;
    public $des;
    public $permissions;

    const SCENARIO_ADD = 'add';

    public function rules()
    {
        return [
            [['name','des'],'required'],
            ['permissions','safe'],
            ['name','validateName','on'=>self::SCENARIO_ADD],//设置场景
        ];
    }

    //验证权限名称
    public function validateName(){
        if(\Yii::$app->authManager->getRole($this->name)){
            $this->addError('name','该角色名称已存在!');
        }
    }

    public function attributeLabels()
    {
        return [
            'name'=>'角色名称',
            'des'=>'角色描述',
            'permissions'=>'选择权限',
        ];
    }

    public static function getPermission(){
        $permissions=\Yii::$app->authManager->getPermissions();
        $role=[];
        foreach ($permissions as $permission){
            $role[$permission->name]=$permission->description;
        }
        return $role;
    }
}