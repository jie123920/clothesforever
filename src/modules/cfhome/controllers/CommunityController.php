<?php
namespace app\modules\cfhome\controllers;
class CommunityController extends CommonController {
    public $defaultAction = 'index';
    public function init()
    {
        parent::init();
    }
    public function actionIndex() {
        $this->isLogin();
        $this->view->params['meta_title'] = 'Clothes Forever –The Best Dress up and Fashion Games for Girls';
        $this->view->params['keyword'] = "Clothes Forever, Clothes Forever app, dress up games, fashion games, make up games, fashion apps, clothing apps, online girl games, fashion designing games";
        $this->view->params['description'] = "Play fashion games on Clothes Forever. Dressing up your favorite characters, celebritites, fashion and more. Fashion competitions with friends!";
        return $this->render('index.html', [
        ]);

    }
    
}