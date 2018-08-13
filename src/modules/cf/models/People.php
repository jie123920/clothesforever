<?php
namespace app\modules\cf\models;
class People extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%people}}';
    }

    public function getList()
    {
        $cacheKey = 'web_people_list';
        if ($data = \Yii::$app->cache->get($cacheKey)) {
            return unserialize($data);
        }
        $data = self::find()
            ->select('*')
            ->from(self::tableName())
            ->orderBy(self::tableName().".sort desc")
            ->all();

        if($data){
            \Yii::$app->cache->set($cacheKey, serialize($data), 180);
        }
        return $data;
    }
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['table_id' => 'id'])
            ->where(['table_name'=>'ww2_people','language'=>LANG_SET]);
    }
}
