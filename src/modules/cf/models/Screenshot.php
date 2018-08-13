<?php
namespace app\modules\cf\models;
class Screenshot extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%screenshot}}';
    }

    /**
     * @param int $tid 默认是9 属于游戏平台的
     * @param int  $type 图片类型：1 游戏截图  2 头图轮播图  3 大小轮播图
     * @return array
     */
    public function getList($tid=26,$type=1)
    {
        $cacheKey = 'web_screenshot_list_'.$tid.$type;
        if ($data = \Yii::$app->cache->get($cacheKey)) {
            return $data;
        }
        $where = array(
            'tid'  => $tid,
            'type'  => $type,
        );
        $data = (new \yii\db\Query())
            ->select('*')
            ->from(self::tableName())
            ->where($where)
            ->orderBy(self::tableName().".id desc")
            ->all();

        if($data){
            \Yii::$app->cache->set($cacheKey, $data, 180);
        }
        return $data;
    }

    public function getdaxiao($tid=26)
    {
        $cacheKey = 'web_screenshot_daxiao_'.$tid;
        if ($data = \Yii::$app->cache->get($cacheKey)) {
            return unserialize($data);
        }
        $where = array(
            'tid'  => $tid,
            'type'  => 3,
        );
        $data = self::find()
            ->select('*')
            ->from(self::tableName())
            ->where($where)
            ->orderBy(self::tableName().".id desc")
            ->all();

        if($data){
            \Yii::$app->cache->set($cacheKey, serialize($data), 180);
        }
        return $data;
    }
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['table_id' => 'id'])
            ->where(['table_name'=>'ww2_screenshot','language'=>LANG_SET]);
    }
}
