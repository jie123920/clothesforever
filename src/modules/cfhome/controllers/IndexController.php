<?php

namespace app\modules\cfhome\controllers;
use app\helpers\myhelper;
use app\modules\cf\models\Video;
use \app\modules\cf\models\News_Multi_Language;
class IndexController extends CommonController
{
    public $defaultAction = 'index';
    public function init() {
        parent::init();
        $this->view->params['meta_title'] = 'Clothes Forever –The Best Dress up and Fashion Games for Girls';
        $this->view->params['keyword'] = "Clothes Forever, Clothes Forever app, dress up games, fashion games, make up games, fashion apps, clothing apps, online girl games, fashion designing games";
        $this->view->params['description'] = "Play Clothes Forever!  The exciting new fashion game where you can dress up the hottest models, hang with celebs, travel the world and more!  Fashion competitions are waiting for you, so don't delay!";
        $this->view->params['active_index'] = '1';
    }

    public function actionIndex()
    {
        $this->layout = '@module/views/'.GULP.'/public/main.html';

        $code = \YII::$app->request->get('code','');
        $email = \YII::$app->request->get('email','');
        $get_password_code  = false;
        if (!empty($code) && !empty($email)) {
            $verify = myhelper::verify_resetpwd_code($email,$code);
            if ($verify) {
                $get_password_code = true;
                $get_password_js = '<script>showDialog("#reset_pwd")</script>';
                $this->view->params['get_password_js']= $get_password_js;
            } else {
                $get_password_js = '<script>layer_alert("Please request another password recovery email.",1,"/index/index");</script>';
                $this->view->params['get_password_js']= $get_password_js;
            }
        }
        $this->view->params['get_password_code']= $get_password_code;
        $this->view->params['email']= $email;
        $videoModel = new Video;
        $videoList  = $videoModel->getVideoList(1);

        $articleModel = new News_Multi_Language;
        $newsList  = $articleModel->getArticleList(NULL,3,true,0,LANG_SET,1);//TODO  26
        if(empty($newsList)) $articleModel->getArticleList(NULL,3,true,0,'en-us',1);//TODO  26
        return $this->render('index.html', [
            'videoList'=>$videoList,
            'newsList'=>$newsList
        ]);
    }
}
