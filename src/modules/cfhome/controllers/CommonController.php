<?php
namespace app\modules\cfhome\controllers;
use app\modules\cfhome\services\Cart;
use yii\web\Controller;
use app\helpers\myhelper;

class CommonController extends Controller {
    public $enableCsrfValidation = false;//CSRF
    public $view = NULL;
    public $cookies = NULL;//read
    public $cookies_2 = NULL;//write
    public $sessions = NULL;
    public $layout = '';
    public $ucenter = null;
    public $user_info = [];
    public $is_login = 0;
    public $game_list = [];
    public function init(){
        $this->layout = '@module/views/'.GULP.'/public/main.html';
        $this->ucenter = new \Ucenter\User(['env'=>ENV,'domain'=>DOMAIN]);
        $this->view = \Yii::$app->view;
        $this->cookies = \Yii::$app->request->cookies;
        $this->cookies_2 = \Yii::$app->response->cookies;
        $this->sessions = \Yii::$app->session;
        $this->game_list = [
            [
                'id'=>7,
                'game_name'=>'ClothesForever'
            ],
        ];
        $this->view->params['is_login']= 0;
        $this->view->params['login_show']= 0;
        $this->view->params['user_info']= [];
        $this->view->params['ticket_count']= 0;
        $this->view->params['system_msg_count']= 0;
        $this->view->params['cart_num']= 0;
        $user_info = $this->getCookieUser();//判断用户是否登录
        if ($user_info) {
            $this->view->params['is_login'] = $this->is_login = 1;
            $this->view->params['user_info'] = $this->user_info = $user_info;
            $this->view->params['cart_num'] = (new Cart())->cartCount(1, $user_info['id']);


            $data = myhelper::sendRequest(['uid'=>$user_info['id'],'game_id'=>7],'GET',true,\Yii::$app->params['MY_URL']['MUTANTBOX'].'/api/ticket/get-count');

            $this->view->params['message_count']= 0;
            $this->view->params['ticket_count']= 0;
            $this->view->params['system_msg_count']= 0;
            if($data && isset($data['code'])){
                if ($data['code']==0){
                    $this->view->params['message_count']= isset($data['data']['message_count'])?$data['data']['message_count']:0;
                    $this->view->params['ticket_count']= isset($data['data']['ticket_count'])?$data['data']['ticket_count']:0;
                    $this->view->params['system_msg_count']= isset($data['data']['system_msg_count'])?$data['data']['system_msg_count']:0;
                }
            }

        }

        $this->getexChangeRate();
    }

    /**
     * 内部用户退出登录
     */
    public  function logout() {
        $token = $this->sessions['userinfo']['token'];
        if (isset($token)){
            (new \Ucenter\Ucenter(['domain'=>DOMAIN, 'env'=>ENVUC]))->User()->logout($token);
        }
        $this->sessions['userinfo'] = null;
        $this->cookies_2->removeAll();
    }

    /**
     * 检测用户是否登录
     * @return integer 0-未登录，大于0-当前登录用户ID
     */
    function is_login($type = 'user')
    {
        if($this->ucenter->getToken()){
            return true;
        }else {
            if ($this->ucenter->getEncodeCookie('remember_me_token')) {
                return true;
            }
            return 0;
        }
    }

    /* 空操作，用于输出404页面 */
    public function actionEmpty() {
        //header(" HTTP/1.0  404  Not Found");
        return $this->render('/public/404.html', [
            'login_show'=>1
        ]);
        \YII::$app->end();
    }

    public function actionMaintenance() {
        return $this->render('/public/maintenance.html', [
        ]);
        \YII::$app->end();
    }

    //类似于无限极分类的做法，通过子类找父类
    //面包屑导航
    public function getParents ($list, $id) {
        $arr = array();
        foreach ($list as $v) {
            if ($v['id'] == $id) {
                $arr[] = $v;
                $arr = array_merge($this->getParents($list, $v['parent_id']),$arr);
            }
        }
        return $arr;
    }

    protected function getCookieUser()
    {
        if(!isset($_COOKIE['_ttl']) && !isset($_COOKIE['remember_me_token'])){
            return false;
        }
        $userData = $ttl = null;
        if (isset($this->sessions['user_data'])) {
            $userData = $this->sessions['user_data'];
        }
        if (isset($_COOKIE['_ttl'])) {//只存在1天
            $ttl = $_COOKIE['_ttl'];
        }

        if ($userData && $ttl == $userData['ttl'] && $ttl!=null) {
            return $userData;
        }

        $userData = $this->ucenter->userinfo(null, 'id,email,country,username,avatar,gender,birth,skype,mobile,platform');
        if(($userData && $userData['code']!=0) || !$userData){
            $remember_me_token = $this->ucenter->getEncodeCookie('remember_me_token');
            if($remember_me_token){
                $userData = $this->autoLogin($remember_me_token);
                if($userData && $userData['code'] == 0){
                    $userData = $this->ucenter->userinfo($userData['data']['token'], 'id,email,country,username,avatar,gender,birth,skype,mobile,platform');
                    if($userData && $userData['code']!=0){//未获取接口 退出
                        return false;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
        $userData               = $userData['data'];
        $userData['avatar_url'] = $userData['avatar'];
        $userData['birth_data'] = strtotime($userData['birth']);
        $userData['ttl']        = $ttl;
        if($userData['avatar'] == '') $userData['avatar'] = DEFAULT_AVATAR;
        setcookie('user',json_encode($userData),time()+3600*365,'/',DOMAIN);
        $this->sessions['user_data']  = $userData;

        return $userData;
    }

    public function isLogin() {
        if (!($this->is_login)) {
            return $this->redirect(['/login']);\YII::$app->end();
//            $this->view->params['login_show']= 1;
//            return $this->render('/public/404.html', [
//            ]);
//            \YII::$app->end();
        }
    }


    /**
     * 记住我功能
     * @param string $remember_me_token
     */
    public function autoLogin($remember_me_token='') {
        $returnData = (new \Ucenter\Ucenter(['domain'=>DOMAIN, 'env'=>ENVUC]))->User()->autoLogin($remember_me_token);
        if($returnData && $returnData['code']==0){
            $data = $returnData['data'];
            $this->sessions['userinfo'] = $data;
            $this->sessions['user_data'] = null;

            $expire =  60 * 60 * 24;//1 day
            $auth = array(
                'uid' => $data['uid'],
                'username' => !empty($data['username'])?$data['username']:$data['account'],
                'thumb_avatar'=>$data['avatar'],
                'email' => $data['account'],
                'last_login_time' => isset($data['lasttime'])?$data['lasttime']:time(),
            );

            $this->cookies_2->add(new \yii\web\Cookie([
                'name' => 'user_auth',
                'value' => $auth,
                'expire'=>time()+$expire,
                'domain'=>\YII::$app->params['COOKIE_DOMAIN'],
            ]));
            $this->cookies_2->add(new \yii\web\Cookie([
                'name' => 'user_auth_sign',
                'value' => myhelper::data_auth_sign($auth),
                'expire'=>time()+$expire,
                'domain'=>\YII::$app->params['COOKIE_DOMAIN'],
            ]));

            $this->cookies_2->add(new \yii\web\Cookie([
                'name' => 'last_update_time',
                'value' => time(),
                'domain'=>\YII::$app->params['COOKIE_DOMAIN'],
            ]));
        }
        return $returnData;
    }


    public function error($title){
        $this->layout = '@module/views/'.GULP.'/public/main.html';
        return $this->render('/public/404.html', [
            'title'=>$title,
        ]);
    }



    /**
     * 定义汇率常量
     * 2017年4月27日 上午10:44:19
     * @author liyee
     */
    private function getexChangeRate() {
        $id = isset($_COOKIE['think_rate_id'])?$_COOKIE['think_rate_id']:'';
        $list = myhelper::ccurrcyList($id);
        $default = $list['default'];

        $name = isset($_COOKIE['think_rate_name'])?$_COOKIE['think_rate_name']:$default['name'];
        $m = isset($_COOKIE['think_rate_m'])?$_COOKIE['think_rate_m']:$default['m'];
        $symbol = isset($_COOKIE['think_rate_symbol'])?$_COOKIE['think_rate_symbol']:$default['symbol'];
        $region_name = isset($_COOKIE['think_rate_region_name'])?$_COOKIE['think_rate_region_name']:$default['region_name'];

        define('THINK_RATE_NAME', $name);
        define('THINK_RATE_M', $m);
        define('THINK_RATE_SYMBOL', $symbol);
        define('THINK_RATE_REGION_NAME', $region_name);
    }

    /**
     * 检测登录
     * @param string $referer
     * @param bool $isAjax
     * @return array
     */
    public function check_user($referer='/',$isAjax=false){
        if(!$this->is_login()){
            if($isAjax){
                echo json_encode([ 'code' => -1, 'message' => 'please log in', 'data'    => '' ]) ;exit;
            }else{
                $this->redirect(['/login?referer='.$referer]);\YII::$app->end();
            }
        }
    }

    /**
     * 返回数据
     * @param int $code
     * @param string $message
     * @param array $data
     * @return array
     */
    public function result($code = 0, $message = '', $data = [])
    {
        return [
            'code'    => intval($code),
            'message' => strval($message),
            'data'    => (array)$data,
        ];
    }
}
