<?php
namespace app\modules\cf\controllers;
use \app\modules\cfhome\controllers\CommonController;
use \app\modules\cf\models\News_Multi_Language;
use \app\modules\cf\models\ArticleCategory;
use yii\data\Pagination;
class ArticleController extends CommonController
{
    public function init()
    {
        parent::init();
        $this->view->params['meta_title'] = 'Clothes Forever –News';
        $this->view->params['keyword'] = "Clothes Forever, Clothes Forever app, dress up games, fashion games, make up games, fashion apps, clothing apps, online girl games, fashion designing games";
        $this->view->params['description'] = "Play Clothes Forever!  The exciting new fashion game where you can dress up the hottest models, hang with celebs, travel the world and more!  Fashion competitions are waiting for you, so don't delay!";
    }

    public function actionIndex()
    {
        $fid         = \Yii::$app->request->get('fid',"'".EVENTS_ID."','".ANNOUNCEMENT_ID."','".GUIDE_ID."','".STRATEGY_ID."'");
        //active menu
        if($fid == GUIDE_ID){
            $this->view->params['active_guide'] = '1';
        }else{
            $this->view->params['active_news'] = '1';
        }
        $article     = new News_Multi_Language;
        if(\Yii::$app->request->isAjax) {
            $fid = \Yii::$app->request->post('fid', "'".EVENTS_ID."','".ANNOUNCEMENT_ID."','".GUIDE_ID."','".STRATEGY_ID."'");
            $per_page = intval(\Yii::$app->request->post('page', 1) - 1) * 10;
            $articleList = $article->getArticleList($fid, 10, false,$per_page , LANG_SET, TID,false);
            $count = $article->getCount($fid, false, LANG_SET, TID);
            if ($count['0']['num'] < 1) {
                $articleList = $article->getArticleList($fid, 10, false, $per_page, 'en-us', TID,false);
                $count = $article->getCount($fid,false,'en-us',TID);
            }
            $this->layout = false;
            return $this->render('newslist_son.html', [
                'articlerList'=>$articleList,
            ]);
        }

        $articleList = $article->getArticleList($fid,10,false,intval(\Yii::$app->request->post('page',1)-1)*10,LANG_SET,TID,false);
        $count       = $article->getCount($fid,false,LANG_SET,TID);
        if($count['0']['num']<1){
            $articleList = $article->getArticleList($fid,10,false,intval(\Yii::$app->request->post('page',1)-1)*10,'en-us',TID,false);
            $count       = $article->getCount($fid,false,'en-us',TID);
        }
        $pages       = new Pagination(['totalCount' =>$count[0]['num'], 'pageSize' => 10,'route'=>'/news']);
        //所有分类
        $ArticleCategory = new ArticleCategory;
        $cat_list = (new \yii\db\Query())
            ->select('*')
            ->from($ArticleCategory::tableName())
            ->all();

        $arrPids = $this->getParents($cat_list, $fid);
        $cate_name = \YII::t('common','news');
        foreach ($arrPids as $v){
            if($v['id'] == $fid){
                $cate_name = $v['name'];
            }
        }
        return $this->render('newslist.html', [
            'articlerList'=>$articleList,
            'page'=>$pages,
            'fid'=>$fid,
            'cate_name'=>$cate_name,
            'isLoading'=>$count['0']['num']>10?1:0,
            'arrPids'=>$arrPids,
        ]);
    }

    public function actionArticle()
    {
        $pid = \Yii::$app->request->get('id',-1);
        $article_obj     = new News_Multi_Language;
        $article = $article_obj->getInfoById($pid);
        if (empty($article)) {
            $article = $article_obj->getInfoById($pid,'en-us');
            if (empty($article)) {
                return $this->redirect(['/404']);
            }
        }
        $this->view->params['meta_title'] = $article['title'];
        $this->view->params['keyword'] = $article['title'];
        $this->view->params['description'] = mb_substr(strip_tags(htmlspecialchars_decode($article['message'])), 0, 160, 'utf8');


        //所有分类
        $ArticleCategory = new ArticleCategory;
        $cat_list = (new \yii\db\Query())
            ->select('*')
            ->from($ArticleCategory::tableName())
            ->all();

        $arrPids = $this->getParents($cat_list, $article['fid']);
        $category = end($arrPids);
        if(in_array($category['name'],['Account','Payment','Game'])){
            $this->view->params['active_support'] = '1';
        }elseif(in_array($category['name'],['Guide'])){
            $this->view->params['active_guide'] = '1';
        }else{
            $this->view->params['active_news'] = '1';
        }

        return $this->render('newsdetail.html', [
            'article'=>$article,
            'arrPids'=>$arrPids,
        ]);
    }

    public function actionGuide()
    {
        $this->view->params['meta_title'] = \yii::t('common', 'TitLiberatorsReview');
        $this->view->params['keyword'] = "liberators review,liberators events,wargames,ww2 games,mutantbox";
        $this->view->params['description'] = "Read all latest news about liberators from mutantbox.com.  All liberators review and liberators events, gaming industry news and more.More details click here! ";

        $article     = new News_Multi_Language;
        $articleList = $article->getArticleList('12,13,22',10,false,intval(\Yii::$app->request->get('page',1)-1)*10);
        $count       = $article->getCount('12,13,22',false);
        if($count<1){//为空的时候 默认英语
            $articleList = $article->getArticleList('12,13,22',10,false,intval(\Yii::$app->request->get('page',1)-1)*10,'en-us');
            $count       = $article->getCount('12,13,22',false,'en-us');
        }
        $pages       = new Pagination(['totalCount' =>$count, 'pageSize' => 10]);
        return $this->render('newslist.html', [
            'articlerList'=>$articleList,
            'page'=>$pages,
        ]);
    }


    public function actionGameinfo(){
        return $this->render('gameinfo.html', [

        ]);
    }

}
