<?php

namespace app\modules\cfhome\controllers;
use app\modules\cf\models\News_Multi_Language;
use app\modules\cf\models\ArticleCategory;
class FaqController extends CommonController
{
    public function init()
    {
        parent::init();
        $this->view->params['meta_title'] = 'Clothes Forever –FAQ';
        $this->view->params['keyword'] = "Clothes Forever, Clothes Forever app, dress up games, fashion games, make up games, fashion apps, clothing apps, online girl games, fashion designing games";
        $this->view->params['description'] = "Play fashion games on Clothes Forever. Dressing up your favorite characters, celebritites, fashion and more. Fashion competitions with friends!";
        $this->view->params['active_support'] = '1';
    }

    public function actionDetails()
    {

        $where['pid']      = \YII::$app->request->get('id');
        $where['language'] = LANG_SET;
        $article = (new \yii\db\Query())
            ->select('*')
            ->from(News_Multi_Language::tableName())
            ->where($where)
            ->one();
        if (empty($article)) {
            $where['language'] = 'en-us';
            $article = (new \yii\db\Query())
                ->select('*')
                ->from(News_Multi_Language::tableName())
                ->where($where)
                ->one();
            if (empty($article)) {
                return $this->redirect(['/404']);
            }
        }
        //所有分类
        $ArticleCategory = new ArticleCategory;
        $cat_list = (new \yii\db\Query())
            ->select('*')
            ->from($ArticleCategory::tableName())
            ->all();

        $arrPids = $this->getParents($cat_list, $article['fid']);
        return $this->render('details.html', [
            'article'=>$article,
            'arrPids'=>$arrPids,
        ]);
    }

    public function actionFaqlist()
    {
        $faq_id              = \YII::$app->request->get("id");
        $keyword             = \YII::$app->request->get("keyword");
        $page['page']        = \YII::$app->request->get('p');
        $page['page_count']  = 4;
        $where['s.language'] = LANG_SET;
        $where['s.tid'] = TID;
        if (!empty($faq_id) || !empty($keyword)) {
            if (!empty($faq_id)) {
                $where['s.fid'] = $faq_id;
            }
            $andWhere = [];
            if (!empty($keyword)) {
                $andWhere = array("LIKE",'s.title', $keyword);
            }
            if (\YII::$app->request->isAjax) {
                $list = $this->page($where, $page, "ajax",$andWhere);
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $list;
            } else {
                $list = $this->page($where, $page,'',$andWhere);
                return $this->render('faqlist.html', [
                    'article_list'=>$list,
                ]);
            }
        } else {
            return $this->redirect(['/404']);
        }
    }

    public function page($where = array(), $page = array(), $type = "",$andWhere=[])
    {
        $simpleArticleModel = new News_Multi_Language;
        $list = $simpleArticleModel::getlist($where, 's.create_time DESC',($page['page']-1)*$page['page_count'],$page['page_count'],$andWhere);
        if(empty($list)){//DEFAULT EN-US
            $where['s.language'] = 'en-us';
            $list = $simpleArticleModel::getlist($where, 's.create_time DESC',($page['page']-1)*$page['page_count'],$page['page_count'],$andWhere);
        }
        if ($type == "ajax") {
            $list = $simpleArticleModel->articleAjax($list);
        } else {
            $list = $simpleArticleModel->artFormatting($list);
        }
        return $list;
    }

}
