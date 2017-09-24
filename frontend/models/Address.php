<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property string $id
 * @property string $name
 * @property string $area
 * @property string $area_tail
 * @property integer $tel
 */
class Address extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'tel','province','city','area'], 'required'],
            [['tel'], 'integer'],
           [['name', 'area_tail'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'province'=>'Province',
            'city'=>'City',
            'area' => 'Area',
            'area_tail' => 'Area Tail',
            'tel' => 'Tel',
        ];
    }

}
