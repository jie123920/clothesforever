<?php

namespace app\modules\cf\models;
class ArticleCategory extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return '{{%article_category}}';
    }

    public function getCat($parent_id, $cat_name)
    {
        $cacheKey = 'news_category_'.$parent_id.'_'.$cat_name;
        if ($data = \Yii::$app->cache->get($cacheKey)) {
            return $data;
        }
        $where['parent_id'] = $parent_id;
        $where['name']      = $cat_name;
        $data = (new \yii\db\Query())
            ->select('id')
            ->from(self::tableName())
            ->where($where)
            ->one();

        if($data){
            \Yii::$app->cache->set($cacheKey, $data, 600);
        }

        return $data;
    }

    public function getCatList($parent_id)
    {
        $cacheKey = 'news_category_list_'.$parent_id.LANG_SET;
        if ($cat_list = \Yii::$app->cache->get($cacheKey)) {
           return $cat_list;
        }

        $where['parent_id'] = $parent_id;
        $cat_list = (new \yii\db\Query())
            ->select('id,name,sort')
            ->from(self::tableName())
            ->where($where)
            ->orderBy("sort ASC,id ASC")
            ->all();

        foreach ($cat_list as $key => $value) {
            $where               = array();
            $where['fid']      = $value['id'];
            $cat_list[$key]['article'] = News_Multi_Language::getlist($where, "order DESC,create_time DESC", 0, 6);
        }
        
        if($cat_list){
            \Yii::$app->cache->set($cacheKey, $cat_list, 600);
        }
        return $cat_list;
    }

    public function getCatByPlatform($platform)
    {
        $cacheKey = 'news_category_'.$platform;
        if ($data = \Yii::$app->cache->get($cacheKey)) {
            return $data;
        }
        $where['platform'] = $platform;
        $where['name'] = 'FAQ';
        $data = (new \yii\db\Query())
            ->select('id')
            ->from(self::tableName())
            ->where($where)
            ->one();

        if($data){
            \Yii::$app->cache->set($cacheKey, $data, 600);
        }

        return $data;
    }



}
