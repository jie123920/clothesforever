<?php
namespace app\Library\ShopPay;

use app\helpers\myhelper;

/**
 *
 * @author dell
 *        
 */
class Common
{
    // TODO - Insert your code here
    private $current_url;
    protected $shop_id;
    private $env = YII_ENV;
    private $key_shop = '1Fq9uZj9JeJPuje2';
    
    /**
     */
    public function __construct ($shop_id)
    {
        // TODO - Insert your code here
        $this->current_url = $this->currentUrl($this->env);
        $this->shop_id = $shop_id;
    }
    
    protected function currentUrl($env){
        $url = [
            'dev' => PROTOCOL.'://devwww.lovecrunch.com',
//             'dev' => PROTOCOL.'://lc.test.com',
            'test' => PROTOCOL.'://testwww.lovecrunch.com',
            'qa' => PROTOCOL.'://testwww.lovecrunch.com',
            'prod' => PROTOCOL.'://www.lovecrunch.com',
        ];
        
        return isset($url[$env])?$url[$env]:$url['prod'];
    }
    
    /**
     * 电商及支付接口数据统一处理
     * 2017年9月7日 上午11:50:41
     * @author liyee
     * @param unknown $action
     * @param array $params
     */
    protected function shopRule($url, $params = [], $method='GET'){
        $url = $this->current_url."/shop/".$url;
        $params = $this->encode($params);
        $data = myhelper::http($url, $params, $method);
        if ($data){
            return json_decode($data, true);
        }else {
            return false;
        }
    }
    
    /**
     * 接口加密
     * 2017年9月5日 下午4:45:21
     * @author liyee
     * @param unknown $params
     * @return unknown
     */
    public function encode($params){
        $post = $params ;
        sort($params,SORT_STRING);
        $sign = md5($this->key_shop. implode("", $params));
        $post['signature'] = $sign;
        return $post;
    }

    /**
     */
    function __destruct ()
    {
        
        // TODO - Insert your code here
    }
}