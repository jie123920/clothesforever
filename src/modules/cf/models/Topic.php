<?php
namespace app\modules\cf\models;
class Topic extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%topic}}';
    }

    /**
     * 不走数据库了
     * @return array
     */
    public static function get(){
        return [
            ['id'=>'1','topic_title'=>'Payment','sort'=>'0'],
            ['id'=>'2','topic_title'=>'Game','sort'=>'1'],
            ['id'=>'3','topic_title'=>'Account','sort'=>'2'],
            ['id'=>'4','topic_title'=>'Shop','sort'=>'3'],
        ];
    }

}
