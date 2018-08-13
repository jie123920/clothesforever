<?php

namespace app\modules\api\controllers;
use app\modules\cf\models\ArticleCategory;
use app\modules\cf\models\News_Multi_Language;
class NewsController extends CommonController
{
    public $defaultAction = 'get-faq';
    public function init() {
        parent::init();
    }

    public function actionGetFaq(){
        $platform      = \YII::$app->request->get("platform", 20);
        $ArticleCategory = new ArticleCategory;

        $faq_id   = $ArticleCategory->getCatByPlatform($platform);
        $faq_list = $ArticleCategory->getCatList($faq_id['id']);
        foreach ($faq_list as &$item) {
            switch ($item['name']) {
                case 'Payment':
                    $item['name'] = \YII::t('common','SupportPayment');
                    break;
                case 'Shop':
                    $item['name'] = \YII::t('common','Shop');
                    break;
            }
        }

        return $this->result(0,$faq_list,'ok');
    }

    public function actionInfo(){
        $where['pid']      = \YII::$app->request->get('id');
        $where['language'] = \YII::$app->request->get('lang','en-us');
        // $where['c.platform']      = \YII::$app->request->get("platform", 20);
        $article = (new \yii\db\Query())
            ->select('a.*')
            ->from(News_Multi_Language::tableName().' as a')
            ->join("INNER JOIN","ww2_article_category as c","c.id=a.fid")
            ->where($where)
            ->one();
 
        if (empty($article)) {
            $where['language'] = 'en-us';
            $article = (new \yii\db\Query())
                ->select('a.*')
                ->from(News_Multi_Language::tableName(). 'as a')
                ->join("RIGHT JOIN","ww2_article_category as c","c.id=a.fid")
                ->where($where)
                ->one();
        }

        return $this->result(0,$article,'ok');
    }


    public function actionFaqList()
    {   
        $faq_id              = \YII::$app->request->get("id");
        $keyword             = \YII::$app->request->get("keyword");
        $page['page']        = \YII::$app->request->get('p',1);
        $page['page_count']  = 100;
        $where['s.language'] = \YII::$app->request->get('lang','en-us');
        $where['c.platform'] = \YII::$app->request->get('platform',20);
        $andWhere = [];
        if (!empty($faq_id) || !empty($keyword)) {
            if (!empty($faq_id)) {
                $where['s.fid'] = $faq_id;
            }
            $andWhere = [];
            if (!empty($keyword)) {
                $andWhere = array("LIKE",'s.title', $keyword);
            }

        }
        $list = $this->page($where, $page,$andWhere);
        return $this->result(0,$list,'ok');
    }

    /**
     * 批量获取
     */
    public function actionBatchFaqList()
    {   
        $faq_id              = \YII::$app->request->get("id");
        $keyword             = \YII::$app->request->get("keyword");
        $page['page']        = \YII::$app->request->get('p',1);
        $page['page_count']  = \YII::$app->request->get('pageSize',1);
        $where['s.language'] = \YII::$app->request->get('lang','en-us');
        $where['c.platform'] = \YII::$app->request->get('platform', 20);
        $where['s.display']  = \YII::$app->request->get('display', 0);
        $andWhere = [];

        if (!empty($faq_id)) {
            $where['s.fid'] = $faq_id;
        }

        if (!empty($keyword)) {
            $andWhere = array("LIKE", 's.title', $keyword);
        }

        $list = $this->page($where, $page, $andWhere);
        return $this->result(0,$list,'ok');
    }

    public function page($where = array(), $page = array(),$andWhere=[])
    {
        $simpleArticleModel = new News_Multi_Language;
        $list = $simpleArticleModel::getlist_($where, 's.order DESC,s.create_time DESC',($page['page']-1)*$page['page_count'],$page['page_count'],$andWhere);
        return $simpleArticleModel->artFormatting($list);
    }
}
