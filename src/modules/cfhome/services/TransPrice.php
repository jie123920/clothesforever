<?php
namespace app\modules\cfhome\services;

use app\helpers\myhelper;

class TransPrice extends Service
{
    /**
     * 添加地址
     * @param array $data
     */
    public function getList(Array $data)
    {
        $data = self::encode($data);
        $result = myhelper::sendRequest($data, 'GET', true, LC_SHOP_API_URL . 'trans-price/index');
        return (array)$result;
    }
}