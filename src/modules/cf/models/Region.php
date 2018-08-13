<?php

namespace app\modules\cf\models;

class Region extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%region}}';
    }
}
