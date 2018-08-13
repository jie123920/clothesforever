<?php
namespace app\modules\cfhome\services;

class Service
{
     public static $uid = 0;
     public static $key = '1Fq9uZj9JeJPuje2';
     public function __construct()
     {
         self::$uid = isset($this->user_info['id'])?$this->user_info['id']:0;
     }

     //接口加密
     public static function encode($params){
         $post = $params ;
         sort($params,SORT_STRING);
         $sign = md5(self::$key. implode("", $params));
         $post['signature'] = $sign;
         return $post;
     }
}