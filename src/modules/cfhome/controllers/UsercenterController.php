<?php
namespace app\modules\cfhome\controllers;
use app\Library\ShopPay\ShopPay;
use app\modules\cf\models\Region;
use app\modules\cf\models\Message;
use app\Library\gameapi\Play;
use app\modules\cf\models\shop_models\ShopOrderSearch;
use app\modules\cf\models\shop_models\GoodsSku;
use app\modules\cf\models\shop_models\GoodsSpecValue;
use app\modules\cf\models\shop_models\Goods;
use app\modules\cf\models\shop_models\ShopRegion as ShopRegion;
use app\helpers\myhelper;
use Yii;
class UsercenterController extends CommonController
{
    public $defaultAction = 'index';
    public function init()
    {
        parent::init();
        $this->layout = '@module/views/'.GULP.'/public/user.html';
        if(!$this->is_login){
            $this->redirect(['/']);\YII::$app->end();
        }

        $user_menu = [];
        $user_menu[0] = array("url" => "/usercenter/index", "name" => \yii::t("common","GeneralSettings"));
        $user_menu[1] = array("url" => "/usercenter/binduser", "name" => \yii::t("common","Connect Accounts"));
        $user_menu[2] = array("url" => "/usercenter/updatepwd", "name" => \yii::t("common","UpdatePassword"));
        $user_menu[3] = array("url" => "/usercenter/orderlist", "name" => \yii::t("common","PaymentHistory"));
        $user_menu[5] = array("url" => "/usercenter/message", "name" => \yii::t("common","System Messages"));
        $user_menu[6] = array("url" => "/usercenter/myorder", "name" => \yii::t("common","My Orders"));
        //$user_menu[7] = array("url" => Yii::$app->params['MY_URL']['SHOP']."/my-favorite", "name" => \yii::t("common","My Favorite Goods"));
        $user_menu[8] = array("url" => "/usercenter/address", "name" => \yii::t("common","Address Book"));
        //$user_menu[9] = array("url" => "/usercenter/contact", "name" => \yii::t("common","Contact Us"));
        $result     = $this->ucenter->getbinded($this->user_info['ttl'], $this->user_info['id']);

        if (!isset($result['code']) || $result['code'] != 0) {
            $result  = $this->ucenter->getbinded(null, $this->user_info['id']);
        }
        if (!isset($result['code']) || $result['code'] != 0) {
            $this->logout();//接口失败 清空所有COOKIE 并退出
            return $this->redirect(['/404']);
        }
        if(!in_array('gw',$result['data'])){
            unset($user_menu[2]);
        }
        foreach ($user_menu as $key => $value) {
            if (strip_tags($_SERVER['REQUEST_URI']) == $value['url']) {
                $user_menu[$key]['active'] = "on";
            }else{
                $user_menu[$key]['active'] = "off";
            }
        }
        $this->view->params['user_menu'] = $user_menu;

        $this->view->params['meta_title'] = 'Clothes Forever –The Best Dress up and Fashion Games for Girls';
        $this->view->params['keyword'] = "Clothes Forever, Clothes Forever app, dress up games, fashion games, make up games, fashion apps, clothing apps, online girl games, fashion designing games";
        $this->view->params['description'] = "Play Clothes Forever!  The exciting new fashion game where you can dress up the hottest models, hang with celebs, travel the world and more!  Fashion competitions are waiting for you, so don't delay!";
    }

    public function actionIndex()
    {
        if (\Yii::$app->request->post()) {
            $result = (new \Ucenter\User(['env'=>ENV]))->updateuser(null, array(
                'username' => \Yii::$app->request->post('username'),
                'gender'   => (int) \Yii::$app->request->post('gender'),
                'birth'    => \Yii::$app->request->post('birth_data'),
                'country'  => \Yii::$app->request->post('region_id'),
                'mobile'   => \Yii::$app->request->post('mobile'),
                'skype'    => \Yii::$app->request->post('skype'),
            ));
            $ajax_data = array('error' => 1, 'msg' => 'Unkonw Error');
            if (isset($result['code']) && $result['code'] == 0) {
                $ajax_data['error'] = 0;
                $ajax_data['msg']   = \YII::t('common','ChangeSuccessful');
                $this->cookies_2->add(new \yii\web\Cookie([
                    'name' => 'last_update_time',
                    'value' => time(),
                    'domain'=>\YII::$app->params['COOKIE_DOMAIN'],
                ]));
                $this->sessions['user_data'] = null;
            } else {
                if (isset($result['error'])) {
                    $ajax_data['msg'] = $result['error'];
                }
            }
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $ajax_data;
        } else {
            $region_list = (new \yii\db\Query())->select("*")->from(Region::tableName())->all();
            return $this->render('index.html', [
                'region_list'=>$region_list,
                'max_age'=>date("Y-m-d", strtotime("-13 year")),
            ]);
        }
    }


    public function actionBinduser()
    {
        $user_info = $this->user_info;
        $userCenter = new \Ucenter\User(['env'=>ENV]);
        $result     = $userCenter->getbinded(null, $user_info['id']);

        if (!isset($result['code']) || $result['code'] != 0) {
            return $this->redirect(['/404']);
        }
        return $this->render('bindUser.html', [
            'account_list'=>$result['data'],
        ]);
    }

    public function actionUpdatepwd()
    {
        $user_info = $this->user_info;
        if (\Yii::$app->request->post()) {
            $result = (new \Ucenter\User(['env'=>ENV]))->updatepwd(null, \Yii::$app->request->post('oldpassword'), \Yii::$app->request->post('password'));

            $ajax_data = array('error' => 1, 'msg' => 'Unkonw Error');
            if (isset($result['code']) && $result['code'] == 0) {
                $ajax_data['error'] = 0;
                $ajax_data['msg']   = \YII::t('common','ChangeSuccessful');
            } else {
                if (isset($result['code']) && $result['code'] == 1027) {
                     $ajax_data['msg'] = 'old password is error';
                }
            }
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $ajax_data;
        } else {

            $userCenter = new \Ucenter\User(['env'=>ENV]);
            $result     = $userCenter->getbinded(null, $user_info['id']);

            if (!isset($result['code']) || $result['code'] != 0) {
                return $this->redirect(['/404']);
            }

            return $this->render('updatePwd.html', [
                'show_oldpassword'=>0,
            ]);
        }
    }

    public function actionOrderlist()
    {
        $user_info    = $this->user_info;
        $page         = 1; //当前页
        $perpage      = 6; //每次显示条数
        $start = ($page - 1) * $perpage;
        $uid          = '\'fb_' . $user_info['id'] . '\',' . '\'gw_' . $user_info['id'].'\','.'\''.$user_info['id'].'\'';
        $payOrderInfo = \Yii::$app->dbpay->createCommand("select * from pay_orders where uid IN($uid) and status !=0 and gameid=7 and source<>'shop' order by createtime DESC limit $start, $perpage")->queryAll();
        if (!empty($payOrderInfo)) {
            foreach ($payOrderInfo as $key => $value) {
                $packid = $value['pack_id'];
                $mealInfo = \Yii::$app->dbpay->createCommand("select * from pay_platform_currency where id=$packid")->queryAll();
                $name = isset($mealInfo[0]['name'])?$mealInfo[0]['name']:'';
                $payOrderInfo[$key]['mealName'] = $name;
                $payOrderInfo[$key]['serverName'] = Play::getserverinfo($value['serverid']);
                if ($payOrderInfo[$key]['currency'] == 'US') {
                    $payOrderInfo[$key]['currency'] = 'USD';
                }
                $payOrderInfo[$key]['price'] = $payOrderInfo[$key]['currency'] . ' ' . $payOrderInfo[$key]['amount'];
            }
        }
        return $this->render('orderList.html', [
            'payOrderInfo'=>$payOrderInfo,
        ]);
    }

    public function actionMoreorder()
    {
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $user_info    = $this->user_info;
            $page         = \Yii::$app->request->post('page'); //当前页
            $perpage      = 6; //每次显示条数
            $start = ($page - 1) * $perpage;
            $uid          = '\'fb_' . $user_info['id'] . '\',' . '\'gw_' . $user_info['id'].'\','.'\''.$user_info['id'].'\'';
            $payOrderInfo = \Yii::$app->dbpay->createCommand("select * from pay_orders where uid IN($uid) and status !=0  and gameid=7 and source<>'shop' order by createtime DESC limit $start, $perpage")->queryAll();
            $str          = '';
            if (!empty($payOrderInfo)) {
                foreach ($payOrderInfo as $key => $value) {
                    $packid = $value['pack_id'];
                    $mealInfo = \Yii::$app->dbpay->createCommand("select * from pay_platform_currency where id=$packid")->queryAll();
                    $name = isset($mealInfo[0]['name'])?$mealInfo[0]['name']:'';
                    $payOrderInfo[$key]['serverName'] = Play::getserverinfo($value['serverid']);
                    $payOrderInfo[$key]['mealName'] = $name;
                    if ($payOrderInfo[$key]['currency'] == 'US') {
                        $payOrderInfo[$key]['currency'] = 'USD';
                    }
                    $payOrderInfo[$key]['price'] = $payOrderInfo[$key]['currency'] . ' ' . $payOrderInfo[$key]['amount'];
                    $status                      = $value['status'] == 1 ? 'Completed' : 'Unfinished';
                    $str .= '<tr class="tr_item tr_item2">'
                    . '<td class="item item01">'
                    . '<h2>Date & Time</h2>'
                    . '<p>' . date("M d, Y", $value['createtime']) . '<br />' . date("H:i:s", $value['createtime']) . '</p>'
                        . '</td>'
                        . '<td class="item item02">'
                        . '<h2>Game</h2>'
                        . '<p>Liberators</p>'
                        . '</td>'
                        . '<td class="item item03">'
                        . '<h2>Server</h2>'
                        . '<p>S' . $value['serverid'] . '</p>'
                        . '</td>'
                        . '<td class="item item04">'
                        . '<h2>Pack</h2>'
                        . '<p>' . $payOrderInfo[$key]['mealName'] . '</p>'
                        . '</td>'
                        . '<td class="item item05">'
                        . '<h2>Price</h2>'
                        . '<p>' . $payOrderInfo[$key]['price'] . '</p>'
                        . '</td>'
                        . '<td class="item item06">'
                        . '<h2>Order ID</h2>'
                        . '<p>' . $value['orderid'] . '</p>'
                        . '</td>'
                        . '<td class="item item07">'
                        . '<h2>Status</h2>'
                        . '<p>' . $status . '</p>'
                        . '</td>'
                        . '</tr>';
                }
            }
            return array('ap_str' => $str);
        }
    }


    public function actionReadmore()
    {
        $where['id'] = \Yii::$app->request->post('msg_id');
        $content = (new \yii\db\Query())
            ->select('content')
            ->from('ww2_message')
            ->where($where)
            ->one();

        $result_data['content']   = htmlspecialchars_decode($content['content']);
        $UserMessage              = new Message;
        $uid                      = $this->user_info['id'];
        $data['is_read']          = 1;
        $UserMessage->updateAll($data,'message_id=:message_id',[':message_id'=>\Yii::$app->request->post("msg_id")]);
        $count_where['uid']           = $uid;
        $count_where['is_read']       = 0;
        $result_data['message_count'] = (new \yii\db\Query())
            ->select('id')
            ->from($UserMessage::tableName())
            ->where($count_where)
            ->count();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $result_data;
    }

    public function actionMessage()
    {
        $uid       = $this->user_info['id'];
        $where['uid'] = $uid;
        $where['game_id'] = 7;
        $data['page']       = \Yii::$app->request->get('p');
        $data['page_count'] = 4;
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $list = $this->page($where, $data, "ajax");
            return $list;
        } else {
            $list = $this->page($where, $data);
            return $this->render('message.html', [
                'message_list'=>$list,
            ]);
        }
    }

    public function page($where = array(), $data = array(), $type = "")
    {
        $data = myhelper::sendRequest(['uid'=>$where['uid'],'game_id'=>$where['game_id'],'p'=>$data['page']],'GET',true,\Yii::$app->params['MY_URL']['MUTANTBOX'].'/api/ticket/get-message');
        $list = isset($data['data'])?$data['data']:[];
        if ($type == "ajax") {
            $list = self::msgAjax($list);
        } else {
            $list = self::msgFormatting($list);
        }
        return $list;
    }



    public static function msgFormatting($list) {
        foreach ($list as $key => $value) {
            if($list[$key]['id'] != 30){
                $list[$key]['source_content'] = strip_tags(htmlspecialchars_decode($value['content']));
            }else{
                $list[$key]['source_content'] = htmlspecialchars_decode($value['content']);
            }
            $list[$key]['content'] = myhelper::msubstr($list[$key]['source_content'], 0,320, "utf-8");
            $list[$key]['send_time'] = date("M d, Y-H:i", $value['send_time']);
        }
        return $list;
    }

    public static function msgAjax($list) {
        $str = "";
        foreach ($list as $key => $value) {
            if($list[$key]['id'] != 30){
                $list[$key]['source_content'] = strip_tags(htmlspecialchars_decode($value['content']));
            }else{
                $list[$key]['source_content'] = htmlspecialchars_decode($value['content']);
            }
            $list[$key]['content'] = myhelper::msubstr($list[$key]['source_content'], 0, 320, "utf-8");
            $list[$key]['send_time'] = date("M d, Y-H:i", $value['send_time']);
            $str .= '<li id="msg' . $value['id'] . '"><div class="img"><img src="/Public/' . BIND_MODULE . '/images/system_pic.jpg"></div><div class="text">';

            if ($value['is_read'] == 1) {
                $str.='<h2 class="is_read">' . $value['title'] . '</h2>';
            } else {
                $str .='<h2 class="msg_title">' . $value['title'] . '</h2>';
            }

            $str .='<div class = "edit">' . $list[$key]['content'] . '<a href = "javascript:void(0);" class = "read_more" data = "' . $value['id'] . '">Read More</a></div>
            <div class = "other">
            <span>System</span><em>|</em><span>' . $list[$key]['send_time'] . '</span>
            </div>
            </div>
            </li>';
        }
        return $str;
    }

    public function actionAjaxregioncode()
    {
        $region_id = \Yii::$app->request->post('region_id');
        if (!empty($region_id)) {
            $where['id']       = $region_id;
            $result = (new \yii\db\Query())
                ->select('area_code')
                ->from(Region::tableName())
                ->where($where)
                ->one();
            $data['area_code'] = $result['area_code'];
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $data;
        }
    }

    public function actionUploadportrait()
    {
        if ($_FILES['portrait_file']) {
            $upload_config = \Yii::$app->params['ARTICLEPICTURE_UPLOAD'];
            $upload        = new \app\helpers\Image\Upload($upload_config); // 实例化上传类
            $info          = $upload->upload();
            $ajax_data = array("result" => "", "msg" => "", "src" => "", "path" => "", "width" => "", "height" => "");
            if (!$info) {
                $ajax_data['result'] = 1;
                $ajax_data['msg']    = $upload->getError();
                echo '<html>
                            <head>
                            <meta charset="UTF-8"><title></title>
                            <script>document.domain = \''.$_SERVER['HTTP_HOST'].'\'</script>
                            </head>
                            <body>'. json_encode($ajax_data) .'</body>
                            </html>';
            } else {
                $ajax_data['result'] = 0;
                $ajax_data['msg']    = "上传成功";
                $ajax_data['src']    = UPLOAD_IMAGE_FILE_URL. $info['portrait_file']['savepath'] . $info['portrait_file']['savename'];
                $ajax_data['path']   = UPLOAD_IMAGE_FILE . $info['portrait_file']['savepath'] . $info['portrait_file']['savename'];

                $image = new \app\helpers\Image\Image();
                $image->open(UPLOAD_IMAGE_FILE . $info['portrait_file']['savepath'] . $info['portrait_file']['savename']);
                $width  = $image->width(); // 返回图片的宽度
                $height = $image->height(); // 返回图片的高度
                $ajax_data['width']  = $width;
                $ajax_data['height'] = $height;

                echo '<html>
                            <head>
                            <meta charset="UTF-8"><title></title>
                            <script>document.domain = \''.$_SERVER['HTTP_HOST'].'\'</script>
                            </head>
                            <body>'. json_encode($ajax_data) .'</body>
                            </html>';
               // $this->ajaxReturn($ajax_data);
            }
        }
    }

    public function actionCropportrait()
    {

        $crop_img = \Yii::$app->request->post();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $image = new \app\helpers\Image\Image();

        $image->open($crop_img['src']);
        $save_dir  = dirname($crop_img['src']);
        $save_name = "thumb_" . basename($crop_img['src']);

        $img_info = $image->crop($crop_img['w'], $crop_img['h'], $crop_img['x'], $crop_img['y'], 200, 200)->save($save_dir . '/' . $save_name);
        if (!$img_info) {
            $ajax_data['result'] = 1;
            $ajax_data['msg']    = $image->getError();
            return $ajax_data;
        } else {
            $user_info                = $this->user_info;
            $dirname                  = array_pop(explode("/", $save_dir));

            $data['id']               = $user_info['id'];
            $data['avatar_url']       = $dirname . "/" . $save_name;
            $data['thumb_avatar_url'] = $dirname . "/" . $save_name;

            //图片同步CDN
            $uploadCDNResult = true;
            $upload_path = dirname(UPLOAD_IMAGE_FILE)."/";
            $need_rsync_path = $upload_path.'images/';
            foreach (\YII::$app->params['RSYNC_CDN_ADDRESS'] as  $address) {
                $address = $address.'/images/';
                system("rsync -avr {$need_rsync_path} {$address} > /dev/null", $systemResult);
                if ($systemResult != 0) {
                    $uploadCDNResult = false;
                    break;
                }else{

                }
            }
            if($uploadCDNResult){
                @unlink($upload_path.'images/'.$dirname . "/" . $save_name);//删除缩略图
                @unlink($upload_path.'images/'.$dirname . "/" . basename($crop_img['src']));//删除原图
                $avatar = UPLOAD_CDN_URL.'/images/'.$dirname . "/" . $save_name;
            }else{
                $ajax_data['result'] = 2;
                $ajax_data['msg']    = "Failed to update profile picture.";
                return $ajax_data;
            }

            $updateData = (new \Ucenter\User(['env'=>ENV]))->updateuser(null, array('id'=>$user_info['id'],'avatar'=>$avatar));
            if (isset($updateData['error']) && empty($updateData['error'])) {
                $ajax_data['result'] = 0;
                $ajax_data['msg']    = "Profile picture updated.";

                $this->cookies_2->add(new \yii\web\Cookie([
                    'name' => 'last_update_time',
                    'value' => time(),
                    'domain'=>\YII::$app->params['COOKIE_DOMAIN'],
                ]));
                $this->sessions['user_data'] = null;
                return $ajax_data;
            } else {
                $ajax_data['result'] = 2;
                $ajax_data['msg']    = "Failed to update profile picture.";
                return $ajax_data;
            }
        }
    }

    public function actionAjaxsaveportrait()
    {
        $avater_src = \Yii::$app->request->post('src');
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (!empty($avater_src)) {
            $user_info                = $this->user_info;
            $data['id']               = $user_info['id'];
            $data['avatar_url']       = basename($avater_src);
            $data['thumb_avatar_url'] = basename($avater_src);
            $updateData = (new \Ucenter\User(['env'=>ENV]))->updateuser(null, array('id'=>$user_info['id'],'avatar'=>$avater_src));
            if (isset($updateData['error']) && empty($updateData['error'])) {
                $this->cookies_2->add(new \yii\web\Cookie([
                    'name' => 'last_update_time',
                    'value' => time(),
                    'domain'=>\YII::$app->params['COOKIE_DOMAIN'],
                ]));
                $this->sessions['user_data'] = null;
                $ajax_data['result'] = 0;
                $ajax_data['msg']    = "Profile picture updated.";
                return $ajax_data;
            } else {
                $ajax_data['result'] = 2;
                $ajax_data['msg']    = "Failed to update profile picture.";
                return $ajax_data;
            }
        } else {
            $ajax_data['result'] = 1;
            $ajax_data['msg']    = "Failed to update profile picture.";
            return $ajax_data;
        }
    }


    public function actionMyorder()
    {
        $page     = Yii::$app->request->get('page', 1);
        $pageSize = Yii::$app->request->get('per-page', 10);
        $uid      = $this->user_info['id'];
        $params = [
            'uid' => $uid,
            'page' => $page,
            'per-page' => $pageSize,
        ];

        $shopPay = new ShopPay(SHOP_ID);
        $returnData = $shopPay->adap('shop', 'list', $params);
//        $returnData = $shopPay->listInfo($uid, $page, $pageSize);
        $orderList = [];
        if ($returnData && $returnData['code'] == 0) {
            $orderList = $returnData['data']['models'];
        }
        return $this->render('myorder.html', [
            'order_list' => $orderList,
        ]);
    }
    //MORE ORDERS
    public function actionMyorder_more(){
        $this->layout = false;
        if(\Yii::$app->request->isAjax){
            $page     = Yii::$app->request->get('page', 1);
            $pageSize = Yii::$app->request->get('per-page', 10);
            $uid      = $this->user_info['id'];
            $params = [
                'uid' => $uid,
                'page' => $page,
                'per-page' => $pageSize,
            ];

            $shopPay = new ShopPay(SHOP_ID);
            $returnData = $shopPay->adap('shop', 'list', $params);
//            $returnData = $shopPay->listInfo($uid, $page, $pageSize);
            $orderList = [];
            if ($returnData && $returnData['code'] == 0) {
                $orderList = $returnData['data']['models'];
            }

            return $this->render('_orders.html', [
                'order_list' => $orderList
            ]);
        }
    }

    private function _getOrders($page){
        $count = ShopOrderSearch::find()
            ->select("id")
            ->where(['uid'=>$this->user_info['id'],'shop_id'=>1])
            ->count();
        if($count <= ($page-1)*10) return [];
        $returnData = [];
        $dataProvider = [];
        $_GET['page'] = $page?$page:1;
        $_GET['per-page'] = 10;
        $_GET['ShopOrderSearch']['uid'] = $this->user_info['id'];
        $searchModel                        = new ShopOrderSearch();
        $dataProvider                       = $searchModel->search($_GET);
        $pageSize                           = \Yii::$app->request->get($dataProvider->Pagination->pageSizeParam);
        $dataProvider->Pagination->pageSize = $pageSize ? $pageSize : $dataProvider->Pagination->pageSize;


        //翻译
        foreach ($dataProvider->getModels() as $i=>$model) {
            $data = $model->toArray();
            if(empty($data)) continue;
            $products = json_decode($data['products'],true);
            foreach ($products as &$p){
                if(!isset($p['goods_sku_id'])) continue;
                $sku_info = GoodsSku::findOne($p['goods_sku_id']);
                if(empty($sku_info)) continue;

                $spec_arr = [];
                foreach ($sku_info->spec as $spec_value){
                    $spec_value_info = GoodsSpecValue::findOne($spec_value->spec_value_id);
                    if(empty($spec_value_info)) continue;
                    $spec_value = $spec_value_info->getLanguage(LANG_SET)->one();
                    $spec_name = $spec_value_info->category->getLanguage(LANG_SET)->one();
                    $spec_arr[$spec_value_info->spec_id] = [
                        'spec_id'=>$spec_value_info->spec_id,
                        'spec_name' => isset($spec_name->content)?$spec_name->content:$spec_value_info->category->spec_name,
                        'spec_type' => $spec_value_info->category->spec_type,
                        'spec_value' => isset($spec_value->content)?$spec_value->content:$spec_value_info->spec_value,
                    ];
                }
                $p['spec'] = $spec_arr;

                $goods_info = Goods::findOne($sku_info->goods_id);
                if(empty($goods_info)) continue;
                $p['cover'] = $goods_info->cover;
                //商品名称的翻译
                if($goods_info->language){
                    foreach ($goods_info->language as $l){
                        if($l->table_field == 'name'){
                            $p['name'] = $l->content;
                        }
                    }
                }
            }

            $data['products'] = json_encode($products);

            $country = ShopRegion::findOne(intval($data['country']));
            $address = $data['shipping_address_1'] ? $data['shipping_address_1'].',':'';
            $address .= $data['shipping_address_2'] ? $data['shipping_address_2'].',':'';
            $address .= $data['city'] ? $data['city'].',':'';
            $address .= $country->region_name;

            $data['address'] = $address;
            $data['channel_method'] = myhelper::payinfo('payway',$data['channel_method']);
            $returnData[$i] = $data;
            //$returnData[$i]['statusDetail'] = $model->statusDetail->toArray();
        }
        return $returnData;
    }

    public function actionAddress()
    {
        return $this->render('address.html');
    }

    public function actionContact()
    {
        return $this->render('contact.html', [
        ]);
    }

}
