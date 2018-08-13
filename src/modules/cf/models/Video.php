<?php
namespace app\modules\cf\models;
class Video extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%video}}';
    }

    /**
     * @param int $tid 默认是9 属于游戏平台的
     * @return array
     */
    public function getVideoList($tid=9)
    {
        $cacheKey = 'video_list_'.$tid;
        if ($data = \Yii::$app->cache->get($cacheKey)) {
            return $data;
        }
        $where = array(
            'display'  => 0,
            'tid'  => $tid,
        );
        $data = (new \yii\db\Query())
            ->select(self::tableName().'.*,ww2_photo.img_source')
            ->from(self::tableName())
            ->where($where)
            ->andWhere(['<>','cover_id',0])
            ->join('LEFT JOIN','ww2_photo','ww2_photo.id='.self::tableName().'.cover_id')
            ->orderBy(self::tableName().".create_time desc")
            ->all();

        if($data){
            \Yii::$app->cache->set($cacheKey, $data, 600);
        }
        return $data;
    }

}
