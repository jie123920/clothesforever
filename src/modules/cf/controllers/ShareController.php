<?php
namespace app\modules\cf\controllers;
use \app\modules\cfhome\controllers\CommonController;

class ShareController extends CommonController
{
    public function init()
    {
        parent::init();
        $this->layout = '@module/views/'.GULP.'/public/main-share.html';     
    }

    public function actionIndex()
    {

        $content = str_replace(' ','+',\YII::$app->request->get('content', ''));
        parse_str(base64_decode($content), $params);

        $this->view->params['title'] = $params['title'];
        $this->view->params['desc'] = $params['description'];
        $this->view->params['image'] = $params['image'];

        $this->view->params['width'] = isset($params['width']) ? $params['width'] : false;
        $this->view->params['height'] = isset($params['height']) ? $params['height'] : false;

        return $this->render('index.html', [
        ]);
    }

}
