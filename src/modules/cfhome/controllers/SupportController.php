<?php
namespace app\modules\cfhome\controllers;
use app\helpers\myhelper;
use app\modules\cf\models\ArticleCategory;
use app\modules\cf\models\News_Multi_Language;
use app\modules\cf\models\Topic;
class SupportController extends CommonController
{
    public function init()
    {
        parent::init();
        $this->view->params['meta_title'] = 'Clothes Forever –Support';
        $this->view->params['keyword'] = "Clothes Forever, Clothes Forever app, dress up games, fashion games, make up games, fashion apps, clothing apps, online girl games, fashion designing games";
        $this->view->params['description'] = "Play Clothes Forever!  The exciting new fashion game where you can dress up the hottest models, hang with celebs, travel the world and more!  Fashion competitions are waiting for you, so don't delay!";
        $this->view->params['active_support'] = '1';
    }

    public function actionSearchrelated()
    {
        if (\YII::$app->request->isAjax) {
            $keyword         = \YII::$app->request->post("keyword");
            $article_id      = \YII::$app->request->post("id", 9);
            $ArticleCategory = new ArticleCategory;
            $faq_id          = $ArticleCategory->getCat($article_id, "FAQ");
            $search_str      = $ArticleCategory->getSearch($faq_id, $keyword);
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $search_str;
        }
    }

    public function actionIndex()
    {
        $article_id      = \YII::$app->request->post("id", TID);
        $ArticleCategory = new ArticleCategory;

        $faq_id   = $ArticleCategory->getCat($article_id, "FAQ");
        $faq_list = $ArticleCategory->getCatList($faq_id['id']);
        foreach ($faq_list as &$item) {
            switch ($item['name']) {
                case 'Payment':
                    $item['name'] = \YII::t('common','SupportPayment');
                    break;
                case 'Shop':
                    $item['name'] = \YII::t('cf','Shop');
                    break;
                case 'Game':
                    $item['name'] = \YII::t('common','SupportGame');
                    break;
                case 'Account':
                    $item['name'] = \YII::t('common','SupportAccount');
                    break;
            }
        }
        return $this->render('index.html', [
            'faq_list'=>$faq_list,
            'game_list'=>$this->game_list
        ]);
    }

    public function actionMoreticket()
    {
        if (\YII::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $page         = \YII::$app->request->post("page");
            $where['uid'] = $this->user_info['id'];
            $where['page'] = $page;
            $data = myhelper::sendRequest($where,'GET',true,\Yii::$app->params['MY_URL']['MUTANTBOX'].'/api/ticket/get-more-ticket-list');
            $str = isset($data['data'])?$data['data']:[];
            return array('ap_str' => $str);
        }

    }

    public function actionTicketlist()
    {
        if(!$this->is_login){
            $this->redirect(['/']);\YII::$app->end();
        }
        $where['uid'] = $this->user_info['id'];
        $where['game_id'] = 7;
        $data = myhelper::sendRequest($where,'GET',true,\Yii::$app->params['MY_URL']['MUTANTBOX'].'/api/ticket/get-ticket-list');
        $ticket_list = isset($data['data'])?$data['data']:[];

        return $this->render('/support/ticketList.html', [
            'ticket_list'=>$ticket_list,
            'game_list'=>$this->game_list,
        ]);
    }

    public function actionTicketinfo()
    {
        if (!($this->is_login)) {
            return $this->redirect(['/']);
        }
        $id                = \YII::$app->request->get("id");
        $data = myhelper::sendRequest(['uid'=>$this->user_info['id'],'id'=>$id,'username'=>$this->user_info['username']],'GET',true,\Yii::$app->params['MY_URL']['MUTANTBOX'].'/api/ticket/get-ticket-info');
        $reply_list = isset($data['data'])?$data['data']:[];
        return $this->render('/support/ticketInfo.html', [
            'reply_list'=>$reply_list,
            'game_list'=>$this->game_list,
        ]);
    }

    public function actionSubreply()
    {
        if (!($this->is_login)) {
            return $this->redirect(['/']);
        }
        if (\YII::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $fid        = \YII::$app->request->post("forum_id");
            $content = \YII::$app->request->post("content");
            $data = myhelper::sendRequest(['uid'=>$this->user_info['id'],'forum_id'=>$fid,'content'=>$content],'GET',true,\Yii::$app->params['MY_URL']['MUTANTBOX'].'/api/ticket/subreply');

            if ($data['code'] == 0) {
                $result_data['error'] = 0;
                $result_data['msg']   = \YII::t("common","Thank you, your ticket has been sent") . $fid;
                return $result_data;
            } else {
                $result_data['error'] = 1;
                $result_data['msg']   = 'error';
                return $result_data;
            }
        }
    }

    public function actionSubsolved()
    {
        if (!($this->is_login)) {
            return $this->redirect(['/']);
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id        = \YII::$app->request->post("id");
        $is_solved = \YII::$app->request->post("is_solved");
        $score        = \YII::$app->request->post("score");


        $data = myhelper::sendRequest(['id'=>$id,'uid'=>$this->user_info['id'],'is_solved'=>$is_solved,'score'=>$score],'GET',true,\Yii::$app->params['MY_URL']['MUTANTBOX'].'/api/ticket/subsolved');

        if ($data['code'] == 0) {
            $result_data['error'] = 0;
            $result_data['msg']   = $data['message'];
            return $result_data;
        } else {
            $result_data['error'] = 1;
            $result_data['msg']   = $data['message'];
            return $result_data;
        }
    }

    public function actionTicketcheck()
    {
        if (\YII::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $result_data = [];
            if($this->is_login){
                $result_data['error'] = 0;
            } else {
                $result_data['error'] = 1;
            }
            return $result_data;
        }
    }


    public function actionTicket()
    {
        if(!$this->is_login){
            return $this->isLogin();
        }
        if (\YII::$app->request->isAjax) {
            //防止重复提交START
            if(\Yii::$app->cache->get('cf-website_'.__SELF__.'_uid_'.$this->user_info['id'])){
                return false;
            }
            \Yii::$app->cache->set('cf-website_'.__SELF__.'_uid_'.$this->user_info['id'], 1, 5);
            //防止重复提交END
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $data = \Yii::$app->request->post();

            $data['uid'] = $this->user_info['id'];

            unset($data['client']);

            if(empty($data['game_name'])){
                $data['game_name'] = 'NULL';//数据库 不能为空的
            }
            $data['add_time'] = time();
            $data['server_id'] = 0;
            $data['forum_id'] = substr(strtoupper(md5(uniqid(mt_rand(), true))),mt_rand(0, 20),12);
            $data['clientinfo'] = 'PC';
            $data['language'] = LANG_SET;

            $data = myhelper::sendRequest($data,'GET',true,\Yii::$app->params['MY_URL']['MUTANTBOX'].'/api/ticket/insert');

            if($data && $data['code'] == 0){
                $result_data['error'] = 0;
                $result_data['msg']   =  \YII::t('common','Thank you, your ticket has been sent');
                return $result_data;
            }else{
                $result_data['error'] = 1;
                $result_data['msg']   =  $data['message'];
                return $result_data;
            }
        }


        $topic_list = (new \yii\db\Query())
            ->select('*, pid AS id')
            ->from(News_Multi_Language::tableName())
            ->where(array(
                'is_hot'   => 1,
                'language' => LANG_SET,
                'tid'=>TID
            ))
            ->orderBy('create_time DESC')
            ->limit(10)
            ->all();

        $topic_List = Topic::get();

        return $this->render('ticket.html', [
            'allServerLists'=>[],
            'topic_list'=>$topic_list,
            'topic_List'=>$topic_List,
            'game_list'=>$this->game_list,
            'last_enter_server'=>0
        ]);

    }

}
