<?php

namespace app\modules\cfhome\controllers;

use app\modules\cfhome\services\TransPrice;
use Yii;

/**
 * TransPriceController implements the CRUD actions for TransPrice model.
 */
class TransPriceController extends CommonController
{
    public $defaultAction = 'index';

    public function beforeAction($action)
    {
        Yii::$app->response->format = yii\web\response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    /**
     * Lists all TransPrice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $params = Yii::$app->request->queryParams;
        $transPriceService = new TransPrice();
        return $transPriceService->getList($params);
    }
}
