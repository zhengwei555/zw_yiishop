<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/17
 * Time: 19:23
 */
namespace backend\filters;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

class RbacFilters extends ActionFilter{
    public function beforeAction($action)
    {
       // return true;
        if(!\Yii::$app->user->can($action->uniqueId)) {
            if(\Yii::$app->user->isGuest){
                return $action->controller->redirect(\Yii::$app->user->loginUrl)->send();
            }
            throw new ForbiddenHttpException('你没有该操作权限');
        }
        return parent::beforeAction($action);
    }
}