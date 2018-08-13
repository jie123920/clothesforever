<?php

namespace app\modules\cf\models;

class Language extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%language}}';
    }

    public function scenarios() {
        return [
            'create' => ['id','language','table_name','table_id','table_field','content','created_time','updated_time'],
            'update' => ['id','language','table_name','table_id','table_field','content','created_time','updated_time'],
        ];
    }
}
