<?php
namespace app\modules\cf\controllers;
use app\modules\cf\models\People;
use \app\modules\cfhome\controllers\CommonController;
use \app\modules\cf\models\News_Multi_Language;
use \app\modules\cf\models\Video;
use \app\modules\cf\models\Screenshot;
use \app\helpers\myhelper;

class IndexController extends CommonController
{
    public function init()
    {
        parent::init();
        $this->layout = '@module/views/'.GULP.'/public/main.html';
        $this->view->params['meta_title'] = 'Clothes Forever –The Best Dress up and Fashion Games for Girls';
        $this->view->params['keyword'] = "Clothes Forever, Clothes Forever app, dress up games, fashion games, make up games, fashion apps, clothing apps, online girl games, fashion designing games";
        $this->view->params['description'] = "Play Clothes Forever!  The exciting new fashion game where you can dress up the hottest models, hang with celebs, travel the world and more!  Fashion competitions are waiting for you, so don't delay!";
        $this->view->params['active_index'] = '1';
    }

    public function actionIndex()
    {
        //\Yii::$app->cache->flush();//TODO
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
                $get_password_js = '<script>layer_alert("Please request another password recovery email.",1,"/");</script>';
                $this->view->params['get_password_js']= $get_password_js;
            }
        }
        $this->view->params['get_password_code']= $get_password_code;
        $this->view->params['email']= $email;



        $videoModel = new Video;
        $videoList  = $videoModel->getVideoList(TID);
        $firstvideo = [];
        if($videoList){
            foreach ($videoList as $v){
                $firstvideo = $v;
                break;
            }
        }

        $People = new People();
        $people_list = $People->getList();


        $Screenshot = new Screenshot;
        $sList  = $Screenshot->getList(TID);//截图
        $daxiaoList  = $Screenshot->getdaxiao(TID,3);//大小轮播图
        $articleModel = new News_Multi_Language;
        $events  = $articleModel->getArticleList(EVENTS_ID,1,true,0,LANG_SET,TID);//events
        if(empty($events)){
            $events  = $articleModel->getArticleList(EVENTS_ID,1,true,0,'en-us',TID);
        }
        $announcements  = $articleModel->getArticleList(ANNOUNCEMENT_ID,1,true,0,LANG_SET,TID);//announcements
        if(empty($announcements)){
            $announcements  = $articleModel->getArticleList(ANNOUNCEMENT_ID,1,true,0,'en-us',TID);
        }
        $style1  = $articleModel->getArticleList(STRATEGY_ID,3,true,0,LANG_SET,TID);//$style1
        if(empty($style1)){
            $style1  = $articleModel->getArticleList(STRATEGY_ID,3,true,0,'en-us',TID);
        }
        $style2  = $articleModel->getArticleList(GUIDE_ID,3,true,0,LANG_SET,TID);//$style2
        if(empty($style2)){
            $style2  = $articleModel->getArticleList(GUIDE_ID,3,true,0,'en-us',TID);
        }
        return $this->render('index.html', [
            'isLogined'       => $this->is_login,
            'firstvideo'     =>$firstvideo,
            'videoList'     =>$videoList,
            'sList'=>$sList,
            'events'=>$events,
            'announcements'=>$announcements,
            'style1'=>$style1,
            'style2'=>$style2,
            'daxiaoList'=>$daxiaoList,
            'people_list'=>$people_list
        ]);
    }


    public function actionDownload(){
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if(strpos($agent, 'iphone') || strpos($agent, 'ipad')){
            $this->redirect(APPLE_APP_URL,301);
        }elseif(strpos($agent, 'android')){
            $this->redirect(GOOGLE_APP_URL,301);
        }else{
            $this->redirect(GOOGLE_APP_URL,301);
        }
    }


    /**
     * 给个地址跳转到电商
     */
    public function actionShop(){
        header('Location: '.\Yii::$app->params['MY_URL']['SHOP']);exit;
    }

}
