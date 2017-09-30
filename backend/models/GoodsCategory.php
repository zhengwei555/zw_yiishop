<?php

namespace backend\models;

use Yii;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "goods_category".
 *
 * @property integer $id
 * @property integer $tree
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $name
 * @property integer $parent_id
 * @property string $intro
 */
class GoodsCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'name', 'parent_id', 'intro'], 'required'],
            [['tree', 'lft', 'rgt', 'depth', 'parent_id'], 'integer'],
            [['intro'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tree' => 'Tree',
            'lft' => 'Lft',
            'rgt' => 'Rgt',
            'depth' => '深度',
            'name' => '商品名称',
            'parent_id' => '上级分类',
            'intro' => '商品简介',
        ];
    }

    public static function getZNodes(){
        $top=['id'=>0,'name'=>'顶级分类','parent_id'=>0];
        $goodsCategories= GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
      //  array_unshift($goodsCategories,$top);
      //  var_dump($goodsCategories);exit();
        return ArrayHelper::merge([$top],$goodsCategories);
    }

    public function behaviors()
    {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
                // 'leftAttribute' => 'lft',
                // 'rightAttribute' => 'rgt',
                // 'depthAttribute' => 'depth',
            ],
        ];
    }
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }

/*    public static function getGoodsCategories()  首页静态化
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $html = $redis->get('goods_categories');
        if($html===false){
            $html = '';
                $goods = self::find()->where(['parent_id'=>0])->all();
            foreach ($goods as $i=>$good1){
                $html .= '<div class="cat '.($i?'':'item1').'">';
                $html .= '<h3><a href="'.Url::to(['shop/list?goods_id='.$good1->id]).'">'.$good1->name.'</a><b></b></h3>';
                $html .= '<div class="cat_detail">';
                foreach ($good1->children(1)->all() as $k=>$good2){
                    $html .= '<dl '.($k?'':'class="dl_1st"').'>';
                    $html .= '<dt><a href="'.Url::to(['shop/list?goods_id='.$good2->id]).'">'.$good2->name.'</a></dt>';
                    $html .= '<dd>';
                    foreach ($good2->children()->all() as $good3){
                        $html .= '<a href="'.Url::to(['shop/list?goods_id='.$good3->id]).'">'.$good3->name.'</a>';
                    }
                    $html .= '</dd>';
                    $html .= '</dl>';
                }
                $html .= '</div>';
                $html .= '</div>';
            }
            //缓存到redis
            $redis->set('goods_categories',$html,24*3600);
        }
        return $html;
    }*/
}

