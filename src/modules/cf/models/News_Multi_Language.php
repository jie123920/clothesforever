<?php

namespace app\modules\cf\models;
use app\helpers\myhelper;
class News_Multi_Language extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%new_simple_article}}';
    }

    //通过fid 获取该分类下面所有语言数据
    public static function getlist($map = array(), $order = 'order DESC', $start = 0, $rows = 10,$andWhere=[])
    {
        $key = 'get_data_by_fid'.implode(',',$map);
        if ($return_list = \Yii::$app->cache->get($key)) {
            return $return_list;
        }
            
        //先找所有的pid
        $data = (new \yii\db\Query())
        ->select('distinct `pid`')
            ->from(self::tableName().'AS s')
            ->where($map)
            ->orderBy("$order")
            ->limit("$rows")
            ->offset("$start")
            ->all();

        //通过pid找到具体内容
        $return_list = [];
        if ($data) {
            foreach ($data as $key => $value) {
                //先找对应语言
                $_where['s.pid'] = $value['pid'];
                $_where['s.language'] = LANG_SET;
                $_data = (new \yii\db\Query())
                ->select('pid AS id, title, s.name, description, remark, fid, tid, pid, mous, is_hot, view, s.create_time, ww2_photo.image, ww2_photo.name AS picname')
                ->from(self::tableName().'AS s')
                ->where($_where)
                ->join('LEFT JOIN','ww2_photo','ww2_photo.id=s.cover_id')
                ->one();

                //再找英语
                if (empty($_data)) {
                    $_where['s.language'] = 'en-us';//默认英语
                    $_data = (new \yii\db\Query())
                    ->select('pid AS id, title, s.name, description, remark, fid, tid, pid, mous, is_hot, view, s.create_time, ww2_photo.image, ww2_photo.name AS picname')
                    ->from(self::tableName().'AS s')
                    ->where($_where)
                    ->join('LEFT JOIN','ww2_photo','ww2_photo.id=s.cover_id')
                    ->one();
                }
                if ($_data) {
                    $return_list[] = $_data;
                }
            }
        }

        if ($return_list) {
            \Yii::$app->cache->set($key, $return_list, 600);
        }

        return $return_list;
    }



    //by tongyonghua
    public static function getlist_($map = array(), $order = 's.create_time DESC', $start = 0, $rows = 10,$andWhere=[])
    {
        $key = __FUNCTION__.'_'.implode(',',$map).$order.$start.$rows.implode(',',$andWhere);
        if ($return_list = \Yii::$app->cache->get($key)) {
            return $return_list;
        }

        if (isset($map['s.fid'])) {
            $map['s.fid'] = explode(',', $map['s.fid']);
        }
        

        $language = $map['s.language'];
        unset($map['s.language']);

        $data = (new \yii\db\Query())
        ->select('distinct `pid`')
            ->from(self::tableName().'AS s')
            ->Join("RIGHT JOIN",'ww2_article_category as c','c.id=s.fid')
            ->where($map)
            ->andWhere($andWhere)
            ->orderBy("$order")
            ->limit("$rows")
            ->offset("$start")
            ->all();

        $return_list = [];
        if ($data) {
            foreach ($data as $key => $value) {
                //先找对应语言
                $_where['s.pid'] = $value['pid'];
                $_where['s.language'] = $language;
                $_data = (new \yii\db\Query())
                ->select('pid AS id, title, s.name, description, remark, fid, tid, pid, mous, is_hot, view, s.create_time, ww2_photo.image, ww2_photo.name AS picname')
                ->from(self::tableName().'AS s')
                ->where($_where)
                ->join('LEFT JOIN','ww2_photo','ww2_photo.id=s.cover_id')
                ->one();

                //再找英语
                if (empty($_data)) {
                    $_where['s.language'] = 'en-us';//默认英语
                    $_data = (new \yii\db\Query())
                    ->select('pid AS id, title, s.name, description, remark, fid, tid, pid, mous, is_hot, view, s.create_time, ww2_photo.image, ww2_photo.name AS picname')
                    ->from(self::tableName().'AS s')
                    ->where($_where)
                    ->join('LEFT JOIN','ww2_photo','ww2_photo.id=s.cover_id')
                    ->one();
                }
                if ($_data) {
                    $return_list[] = $_data;
                }
            }
        }

        if ($return_list) {
            \Yii::$app->cache->set($key, $return_list, 600);
        }

        return $return_list;
    }


    public static function articleAjax($list)
    {
        $str = "";
        foreach ($list as $key => $value) {
            $value['title'] = strtolower($value['title']);
            $value['title'] = preg_replace('/[^a-z0-9\s]/','',$value['title']);
            $value['title'] = preg_replace('/\s+/','-',$value['title']);

            $list[$key]['message']  = strip_tags(htmlspecialchars_decode($value['remark']));
            $list[$key]['message']  = myhelper::msubstr($list[$key]['remark'], 0, 360, "utf-8");
            $list[$key]['url']      = '/news/'.$value['id'].'/'.$value['title'];
            $list[$key]['add_time'] = date('Y m d', $value['create_time']);
//            if (empty($value['images'])) {
//                $list[$key]['images'] = __ROOT__ . '/Public/' . MODULE_NAME . '/images/zwfm.jpg';
//            }

            $str .= '<li>
                <div class="tit">
                    <h2><a href="' . $list[$key]['url'] . '">' . $list[$key]['title'] . '</a></h2>
                </div>
                <div class="text">' . $list[$key]['description'] . '</div>
                <div class="read_more"><a href="' . $list[$key]['url'] . '">' . \YII::t('common','ReadMore') . '</a><font>' . date('Y m d', $list[$key]['create_time']) . '</font></div>
            </li>';

        }

        return $str;
    }

    /* 文章处理 格式化时间 去除HTML */
    public static function artFormatting($list)
    {
        foreach ($list as $key => $value) {
            $value['title'] = strtolower($value['title']);
            $value['title'] = preg_replace('/[^a-z0-9\s]/','',$value['title']);
            $value['title'] = preg_replace('/\s+/','-',$value['title']);

            $list[$key]['message']  = strip_tags(htmlspecialchars_decode($value['remark']));
            $list[$key]['message']  = myhelper::msubstr($list[$key]['remark'], 0, 360, "utf-8");
            $list[$key]['url']      = '/news/'.$value['id'].'/'.$value['title'];
            $list[$key]['add_time'] = date('Y m d', $value['create_time']);
        }
        return $list;
    }



    /**
     * fid是文章分类
     * is_hot=1 & cover_id>0是热点文章
     * $start，$rows 分页
     * show_time是定时发布
     * 整个是先按发布时间create_time排序再按排序数order排序DESC
     */
    public function getArticleList($fid=NULL,$rows=3,$is_hot=true,$start=0,$LANG_SET=LANG_SET,$tid=9,$order_by_order=true)
    {
        $bool_shit = $is_hot==true?1:2;
        $key = 'web_article_list_'.$fid.$rows.$bool_shit.$start.$LANG_SET.$tid;
        if ($data = \Yii::$app->cache->get($key)) {
            return $data;
        }
        $where_is_hot = '';
        if($tid) $tid = "AND s.tid IN(".$tid.")";//根据文章平台分类查询
        if($fid) $fid = "AND s.fid IN(".$fid.")";//根据文章分类查询
        if($is_hot) $where_is_hot = "AND s.cover_id!=0  AND s.is_hot=1";//是否是热点文章
        $where = "WHERE s.display = 0 $tid $fid $where_is_hot AND s.language='".$LANG_SET."' AND  s.show_time is NULL";
        $where .= " OR s.display = 0 $tid $fid $where_is_hot AND s.language='".$LANG_SET."' AND  s.show_time=0";
        $where .= " OR s.display = 0 $tid $fid $where_is_hot AND s.language='".$LANG_SET."' AND s.show_time>0  AND s.show_time<".time();//OR
        $subSql = "SELECT 
                s.pid AS id,
                s.title,
                s.remark,
                s.create_time,
                s.fid,
                s.description,
                if(isnull(s.order),0,s.order) AS r, 
                ww2_article_category.name,
                ww2_photo.image,
                ww2_photo.img_source 
                FROM ww2_new_simple_article AS s  
                LEFT JOIN ww2_photo ON ww2_photo.id=s.cover_id 
                INNER JOIN ww2_article_category ON s.fid = ww2_article_category.id 
                ".$where." 
                ORDER BY s.create_time DESC";



        $sql = $subSql." LIMIT $start , $rows";
        if($order_by_order){//排序字段优先
            $sql = "SELECT * FROM ($subSql) AS t ORDER BY t.r DESC LIMIT $start , $rows";
        }
        $list = \Yii::$app->db->createCommand($sql)->queryAll();

        if($list){//URL
            foreach ($list as &$v){
                $urltitle = $v['title'];
                $urltitle = strtolower($urltitle);
                $urltitle = preg_replace('/[^a-z0-9\s]/','',$urltitle);
                $v['urltitle'] = preg_replace('/\s+/','-',$urltitle);
            }

            \Yii::$app->cache->set($key, $list, 600);
        }

        return $list;
    }


    /**
     * fid是文章分类
     * is_hot=1 & cover_id>0是热点文章
     * show_time是定时发布
     * 整个是先按发布时间create_time排序再按排序数order排序DESC
     */
    public function getCount($fid=NULL,$is_hot=true,$LANG_SET = LANG_SET,$tid=9)
    {
        $key = 'web_article_count_'.$fid.$is_hot.$LANG_SET.$tid;
        if ($data = \Yii::$app->cache->get($key)) {
            return $data;
        }

        $where_is_hot = '';
        if($tid) $tid = "AND s.tid IN(".$tid.")";//根据文章平台分类查询
        if($fid) $fid = "AND s.fid IN(".$fid.")";//根据文章分类查询
        if($is_hot) $where_is_hot = "AND s.cover_id!=0  AND s.is_hot=1";//是否是热点文章
        $where = "WHERE s.display = 0 $tid $fid $where_is_hot AND s.language='".$LANG_SET."' AND  s.show_time is NULL";
        $where .= " OR s.display = 0 $tid $fid $where_is_hot AND s.language='".$LANG_SET."' AND  s.show_time=0";
        $where .= " OR s.display = 0 $tid $fid $where_is_hot AND s.language='".$LANG_SET."' AND s.show_time>0  AND s.show_time<".time();//OR
        $subSql = "SELECT 
                s.pid AS id,
                s.title,
                s.create_time,
                s.fid,
                s.description,
                if(isnull(s.order),0,s.order) AS r, 
                ww2_article_category.name,
                ww2_photo.image,
                ww2_photo.img_source 
                FROM ww2_new_simple_article AS s  
                LEFT JOIN ww2_photo ON ww2_photo.id=s.cover_id 
                INNER JOIN ww2_article_category ON s.fid = ww2_article_category.id 
                ".$where." 
                ORDER BY s.create_time DESC";

        $sql = "SELECT count(*) as num FROM ($subSql) AS t";

        $data =  \Yii::$app->db->createCommand($sql)->queryAll();

        \Yii::$app->cache->set($key, $data, 600);

        return $data;

    }


    public function getInfoById($pid,$LANG_SET = LANG_SET)
    {

        $key = 'news_'.$pid.$LANG_SET;
        if ($data = \Yii::$app->cache->get($key)) {
            return $data;
        }

        $where = " display = 0  AND pid=".$pid." AND language='".$LANG_SET."' AND  show_time is NULL";
        $where .= " OR display = 0 AND pid=".$pid."  AND language='".$LANG_SET."' AND  show_time=0";
        $where .= " OR display = 0  AND pid=".$pid." AND language='".$LANG_SET."' AND show_time>0  AND show_time<".time();//OR
        $data = (new \yii\db\Query())
            ->select('pid AS id, title, remark AS message, ww2_new_simple_article.create_time as create_time, ww2_article_category.name, fid,ww2_photo.img_source as img_url')
            ->from(self::tableName())
            ->join('LEFT JOIN','ww2_photo','ww2_photo.id=ww2_new_simple_article.cover_id')
            ->join('INNER JOIN','ww2_article_category','fid = ww2_article_category.id')
            ->where($where)
            ->one();

        $urltitle = $data['title'];
        $urltitle = strtolower($urltitle);
        $urltitle = preg_replace('/[^a-z0-9\s]/','',$urltitle);
        $data['urltitle'] = preg_replace('/\s+/','-',$urltitle);

        \Yii::$app->cache->set($key, $data, 600);

        return $data;
    }

}
