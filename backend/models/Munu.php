<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "munu".
 *
 * @property integer $id
 * @property string $name
 * @property string $parent_id
 * @property string $url
 * @property integer $sort
 */
class Munu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'munu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'parent_id', 'url', 'sort'], 'required'],
            [['sort'], 'integer'],
            [['name', 'parent_id', 'url'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'parent_id' => '上级菜单',
            'url' => '路由',
            'sort' => '排序',
        ];
    }

    public static function getMunus(){
            $menuItems=[];
            //获取1级菜单
            $munus=Munu::find()->where(['parent_id'=>0])->all();
            foreach ($munus as $munu){
                $children=Munu::find()->where(['parent_id'=>$munu->id])->all();
                $items=[];
                foreach ($children as $child){
                    //判断当前用户有没有权限
                 //   if(Yii::$app->user->can($child->url)){
                        $items[]=['label' => $child->name, 'url'=>[$child->url]];
              //      }
                }
                $menuItems[]= ['label' => $munu->name, 'items'=>$items];
            }
            return $menuItems;
    }


}
