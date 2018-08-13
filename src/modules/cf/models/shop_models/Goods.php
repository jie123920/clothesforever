<?php

namespace app\modules\cf\models\shop_models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "goods".
 *
 * @property string $id
 * @property string $category_id
 * @property integer $people_id
 * @property string $bn
 * @property string $description
 * @property string $name
 * @property string $spec_description
 * @property string $spec_cover
 * @property string $cover
 * @property string $price
 * @property string $store
 * @property integer $status
 * @property string $sort
 * @property string $up_time
 * @property string $down_time
 * @property string $created_time
 * @property string $updated_time
 *
 * @property GoodsPeopleItem[] $goodsPeopleItems
 */
class Goods extends \yii\db\ActiveRecord {
    const PER_PAGE = 12;
    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('db_shop');
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'goods';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['category_id', 'people_id', 'store', 'status', 'sort', 'up_time', 'down_time', 'created_time', 'updated_time'], 'integer'],
            [['description', 'spec_description'], 'string'],
            [['cover'], 'required'],
            [['price','price_original'], 'number'],
            [['bn', 'name', 'cover'], 'string', 'max' => 200],
            [['spec_cover'], 'string', 'max' => 255],
        ];
    }

    public function fields() {
        $field = [
            'id'               => 'id',
            'categoryID'       => 'category_id',
            'BN'               => 'bn',
            'spec_cover'       => 'spec_cover',
            'cover'            => 'cover',
            'price'            => 'price',
            'price_original'            => 'price_original',
            'store'            => 'store',
            'status'           => 'status',
            'sort'             => 'sort',
            'upTime'           => 'up_time',
            'downTime'         => 'down_time',
            'type'             =>'type',
            'link'             =>'link'
        ];

        $langFieldArr = [
            'name'            => 'name',
            'description'     => 'description',
            'specDescription' => 'sepc_description',
        ];
        foreach ($langFieldArr as $showName => $fieldName) {
            $field[$showName] = function ($model, $fieldName) {
                foreach ($model->language as $langModel) {
                    if ($langModel->table_field === $fieldName) {
                        return $langModel->content;
                    }
                }
                return null;
            };
        }

        return $field;
    }


    /**
     * 根据商品分类查询商品列表
     * @param int $category_id
     * @param array $spec
     * @param array $designer
     * @param string $orderBy
     * @param string $lang
     * @param int $page
     * @param int $uid
     * @return array|mixed|\yii\db\ActiveRecord[]
     */
    public static function list_by_category($uid=0,$category_id=0,$spec=[],$designer=[],$orderBy='up_time DESC',$lang='en-us',$page=1){
        $cacheKey = 'shop_goods_list_by_category_'.$category_id.'_'.$orderBy.'_'.$lang.'_'.$page.'_'.$lang;
        if ($return = \Yii::$app->cache->get($cacheKey)) {
           //return $return;
        }
        $return = $where = [];
        $where['goods.status'] = 1;
        if($spec) $where['ii.spec_value_id'] = $spec;//按规格筛选
        if($designer) $where['goods.brand_id'] = $designer;//按品牌筛选
        $where['i.shop_id'] = SHOP_ID;


        if($spec){
            if($orderBy == 'sell DESC'){//按销量查询
                if($category_id){
                    $where = ['i.category_id' => $category_id];
                    $data = self::find()
                        ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                        ->join('LEFT JOIN','goods_properties as p','p.goods_id = goods.id AND p.name="1"')
                        ->orderBy('p.value DESC,goods.up_time DESC')
                        ->where($where)
                        ->limit(self::PER_PAGE)
                        ->offset(($page - 1) * self::PER_PAGE)
                        ->all();
                    $total = self::find()
                        ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                        ->where($where)
                        ->count();
                }else{
                    $data = self::find()
                        ->join('LEFT JOIN','goods_properties as p','p.goods_id = goods.id AND p.name="1"')
                        ->orderBy('p.value DESC,goods.up_time DESC')
                        ->limit(self::PER_PAGE)
                        ->offset(($page - 1) * self::PER_PAGE)
                        ->all();
                    $total = self::find()->count();
                }

            }
            else{
                if($category_id){
                    $where['i.category_id'] = $category_id;
                    $data = self::find()
                        ->distinct()
                        ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                        ->join('RIGHT JOIN','goods_spec_index as ii','ii.goods_id = goods.id')
                        ->where($where)
                        ->orderBy($orderBy)
                        ->limit(self::PER_PAGE)
                        ->offset(($page - 1) * self::PER_PAGE)
                        ->all();
                    $total = self::find()
                        ->distinct()
                        ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                        ->join('RIGHT JOIN','goods_spec_index as ii','ii.goods_id = goods.id')
                        ->where($where)
                        ->count();
                }else{//全部商品
                    $data = self::find()
                        ->distinct()
                        ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                        ->join('RIGHT JOIN','goods_spec_index as ii','ii.goods_id = goods.id')
                        ->where($where)
                        ->orderBy($orderBy)
                        ->limit(self::PER_PAGE)
                        ->offset(($page - 1) * self::PER_PAGE)
                        ->all();
                    $total = self::find()
                        ->distinct()
                        ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                        ->join('RIGHT JOIN','goods_spec_index as ii','ii.goods_id = goods.id')
                        ->where($where)
                        ->count();
                }

            }
        }else{
            if($orderBy == 'sell DESC'){//按销量查询
                if($category_id){
                    $where = ['i.category_id' => $category_id];
                    $data = self::find()
                        ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                        ->join('LEFT JOIN','goods_properties as p','p.goods_id = goods.id AND p.name="1"')
                        ->orderBy('p.value DESC,goods.up_time DESC')
                        ->where($where)
                        ->limit(self::PER_PAGE)
                        ->offset(($page - 1) * self::PER_PAGE)
                        ->all();
                    $total = self::find()
                        ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                        ->where($where)
                        ->count();
                }else{
                    $data = self::find()
                        ->join('LEFT JOIN','goods_properties as p','p.goods_id = goods.id AND p.name="1"')
                        ->orderBy('p.value DESC,goods.up_time DESC')
                        ->limit(self::PER_PAGE)
                        ->offset(($page - 1) * self::PER_PAGE)
                        ->all();
                    $total = self::find()->count();
                }

            }
            else{
                if($category_id){
                    $where['i.category_id'] = $category_id;
                    $data = self::find()
                        ->distinct()
                        ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                        ->where($where)
                        ->orderBy($orderBy)
                        ->limit(self::PER_PAGE)
                        ->offset(($page - 1) * self::PER_PAGE)
                        ->all();
                    $total = self::find()
                        ->distinct()
                        ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                        ->where($where)
                        ->count();
                }else{//全部商品
                    $data = self::find()
                        ->distinct()
                        ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                        ->where($where)
                        ->orderBy($orderBy)
                        ->limit(self::PER_PAGE)
                        ->offset(($page - 1) * self::PER_PAGE)
                        ->all();
                    $total = self::find()
                        ->distinct()
                        ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                        ->where($where)
                        ->count();
                }

            }
        }

        if($data){
            $data = self::_get_spec($data,$lang,$uid);
            $return = ['total_num'=>$total,'page'=>$page,'per_page'=>self::PER_PAGE,'data'=>$data];
            \Yii::$app->cache->set($cacheKey, $return, 600);
        }
        return $return;
    }

    /**
     * 查询商品
     * @param string $keywords
     * @param array $spec
     * @param array $designer
     * @param string $orderBy
     * @param string $lang
     * @param int $page
     * @param int $uid
     * @return array|mixed|\yii\db\ActiveRecord[]
     */
    public static function list_by_search($uid=0,$keywords='',$spec=[],$designer=[],$orderBy='up_time DESC',$lang='en-us',$page=1){
        $cacheKey = 'shop_goods_list_by_search_'.$keywords.'_'.$orderBy.'_'.$lang.'_'.$page;
        if ($return = \Yii::$app->cache->get($cacheKey)) {
            //return $return;
        }
        $return = [];
        $where['goods.status'] = 1;
        $where['l.table_name'] = 'goods';
        $where['l.table_field'] = 'name';
        $where['l.table_field'] = 'name';
        if($spec) $where['ii.spec_value_id'] = $spec;//按规格筛选
        if($designer) $where['goods.brand_id'] = $designer;//按品牌筛选
        $where['i.shop_id'] = SHOP_ID;

        if($spec){
            if($orderBy == 'sell DESC'){//按销量查询
                $data = self::find()
                    ->distinct()
                    ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                    ->join('RIGHT JOIN','goods_spec_index as ii','ii.goods_id = goods.id')
                    ->join('RIGHT JOIN','goods_language as l','l.table_id = goods.id')
                    ->join('LEFT JOIN','goods_properties as p','p.goods_id= goods.id AND p.name="1"')
                    ->where($where)
                    ->andWhere(["like","content",$keywords])
                    ->orderBy('p.value DESC')
                    ->limit(self::PER_PAGE)
                    ->offset(($page - 1) * self::PER_PAGE)
                    ->all();
            }
            else{
                $data = self::find()
                    ->distinct()
                    ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                    ->join('RIGHT JOIN','goods_spec_index as ii','ii.goods_id = goods.id')
                    ->join('RIGHT JOIN','goods_language as l','l.table_id = goods.id')
                    ->where($where)
                    ->andWhere(["like","content",$keywords])
                    ->orderBy($orderBy)
                    ->limit(self::PER_PAGE)
                    ->offset(($page - 1) * self::PER_PAGE)
                    ->all();
            }
            $total = self::find()
                ->distinct()
                ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                ->join('RIGHT JOIN','goods_spec_index as ii','ii.goods_id = goods.id')
                ->join('RIGHT JOIN','goods_language as l','l.table_id = goods.id')
                ->where($where)
                ->andWhere(["like","content",$keywords])
                ->count();
        }else{
            if($orderBy == 'sell DESC'){//按销量查询
                $data = self::find()
                    ->distinct()
                    ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                    ->join('RIGHT JOIN','goods_language as l','l.table_id = goods.id')
                    ->join('LEFT JOIN','goods_properties as p','p.goods_id= goods.id AND p.name="1"')
                    ->where($where)
                    ->andWhere(["like","content",$keywords])
                    ->orderBy('p.value DESC')
                    ->limit(self::PER_PAGE)
                    ->offset(($page - 1) * self::PER_PAGE)
                    ->all();
            }
            else{
                $data = self::find()
                    ->distinct()
                    ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                    ->join('RIGHT JOIN','goods_language as l','l.table_id = goods.id')
                    ->where($where)
                    ->andWhere(["like","content",$keywords])
                    ->orderBy($orderBy)
                    ->limit(self::PER_PAGE)
                    ->offset(($page - 1) * self::PER_PAGE)
                    ->all();
            }
            $total = self::find()
                ->distinct()
                ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                ->join('RIGHT JOIN','goods_language as l','l.table_id = goods.id')
                ->where($where)
                ->andWhere(["like","content",$keywords])
                ->count();
        }


        if($data){
            $data = self::_get_spec($data,$lang,$uid);
            $return = ['total_num'=>$total,'page'=>$page,'per_page'=>self::PER_PAGE,'data'=>$data];
            \Yii::$app->cache->set($cacheKey, $return, 120);
        }
        return $return;
    }



    public static function list_by_favorite($uid,$orderBy='up_time DESC',$lang='en-us',$page=1){
        $cacheKey = 'shop_goods_list_by_favorite_'.$uid.'_'.$orderBy.'_'.$page.'_'.$lang;
        if ($return = \Yii::$app->cache->get($cacheKey)) {
            //return $return;
        }
        if($orderBy == 'sell DESC'){
            $data = Goods::find()
                ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                ->join('RIGHT JOIN','goods_favorite as f','f.goods_id = goods.id')
                ->join('LEFT JOIN','goods_properties as p','p.goods_id = goods.id AND p.name="1"')
                ->orderBy('p.value DESC,goods.up_time DESC')
                ->where(['f.uid'=>$uid,'goods.status'=>1,'i.shop_id'=>SHOP_ID])
                ->limit(self::PER_PAGE)
                ->offset(($page - 1) * self::PER_PAGE)
                ->all();
        }else{
            $data = Goods::find()
                ->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                ->join('RIGHT JOIN','goods_favorite as f','f.goods_id = goods.id')
                ->where(['f.uid'=>$uid,'goods.status'=>1,'i.shop_id'=>SHOP_ID])
                ->orderBy($orderBy)
                ->limit(self::PER_PAGE)
                ->offset(($page - 1) * self::PER_PAGE)
                ->all();
        }

        $total =  GoodsFavorite::find()
            ->where(['uid'=>$uid])
            ->count();
        if($data){
            $data = self::_get_spec($data,$lang,$uid);
            $return = ['total_num'=>$total,'page'=>$page,'per_page'=>self::PER_PAGE,'data'=>$data];
            \Yii::$app->cache->set($cacheKey, $return, 120);
        }
        return $return;
    }


    public static function list_by_recommend($uid=0,$r_id=0,$spec=[],$designer=[],$orderBy='up_time DESC',$lang='en-us',$page=1){
        $cacheKey = 'shop_goods_list_by_recommend_'.$r_id.'_'.$orderBy.'_'.$lang.'_'.$page.'_'.$lang;
        if ($return = \Yii::$app->cache->get($cacheKey)) {
            //return $return;
        }

        $recommend = GoodsRecommend::findOne($r_id);
        $good_ids = json_decode($recommend->good_ids);
        $return = $where = [];
        $where['goods.status'] = 1;
        if($spec) $where['ii.spec_value_id'] = $spec;//按规格筛选
        if($designer) $where['goods.brand_id'] = $designer;//按品牌筛选
        //$where['i.shop_id'] = SHOP_ID;
        $where['goods.id'] = $good_ids;
        if($orderBy == 'sell DESC'){//按销量查询
            $data = self::find()
                ->join('LEFT JOIN','goods_properties as p','p.goods_id = goods.id AND p.name="1"')
                ->where($where)
                ->orderBy('p.value DESC,goods.up_time DESC')
                ->limit(self::PER_PAGE)
                ->offset(($page - 1) * self::PER_PAGE)
                ->all();
            $total = self::find()->where($where)->count();
        }else{
            $data = self::find()
                ->distinct()
                //->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                ->join('RIGHT JOIN','goods_spec_index as ii','ii.goods_id = goods.id')
                ->where($where)
                ->orderBy($orderBy)
                ->limit(self::PER_PAGE)
                ->offset(($page - 1) * self::PER_PAGE)
                ->all();
            $total = self::find()
                ->distinct()
                //->join('RIGHT JOIN','goods_category_index as i','i.goods_id = goods.id')
                ->join('RIGHT JOIN','goods_spec_index as ii','ii.goods_id = goods.id')
                ->where($where)
                ->count();
        }
        if($data){
            $data = self::_get_spec($data,$lang,$uid);
            $return = ['total_num'=>$total,'page'=>$page,'per_page'=>self::PER_PAGE,'data'=>$data];
            \Yii::$app->cache->set($cacheKey, $return, 600);
        }
        return $return;
    }




    /**
     * 获取规格
     * @param array $data
     * @param string $lang
     * @param int $uid
     */
    public static function _get_spec($data,$lang,$uid){
        foreach ($data as &$model){
            $specArr = $specValArr = $productArr = $skuArr = $specValIDArr = [];
            foreach ($model->getSku()->with('specValue')->all() as $skuModel) {
                foreach ($skuModel->specValue as $specValueModel) {
                    if($specValueModel->category->spec_type != 'image'){
                        $specValueModel->spec_image = '';
                    }
                    $specValArr[$specValueModel->spec_id][$specValueModel->spec_value_id] = $specValueModel;
                    $specArr[$specValueModel->spec_id]                                    = $specValueModel->spec_id;
                    $specValIDArr[$specValueModel->spec_value_id] = $specValueModel->spec_value_id;
                }
                $tmpSpecVal = ArrayHelper::getColumn($skuModel->specValue, 'spec_value_id');
                sort($tmpSpecVal);
                $productArr[implode(';', $tmpSpecVal)] = (object) [
                    'price' => $skuModel->price,
                    'count' => $skuModel->store,
                    'id'    => $skuModel->goods_sku_id,
                ];
                $skuArr[$skuModel->goods_sku_id] = $skuModel;
            }
            $specValIDArr = GoodsSpecValue::find()->where(['spec_value_id' => $specValIDArr])->orderBy('sort desc')->all();
            foreach ($specValArr as &$o){//规格值进行排序
                $new_option_ = [];
                foreach ($specValIDArr as $s){
                    if(!isset($o[$s->spec_value_id])) continue;
                    $new_option_[] = $o[$s->spec_value_id];
                }
                $o = $new_option_;
            }
            $new_option = [];
            $specArr = GoodsSpec::find()->where(['spec_id' => $specArr])->orderBy('sort desc')->all();
            foreach ($specArr as $s){//规格分类进行排序
                if(!isset($specValArr[$s->spec_id])) continue;
                $new_option[] = $specValArr[$s->spec_id];
            }

            //临时占用这几个属性为它用
            $model->spec_description = $new_option;//spec
            $model->bn = (object)$productArr;//spec values
            $favorite = self::_get_favorite($uid);
            if(in_array($model->id,$favorite)){
                $model->spec_cover = 1;//if favorite
            }
        }
        //多语言
        foreach ($data as $goods){
            $rec_language = $goods->getLanguage($lang)->all();
            if(!empty($rec_language)) {
                $mapArr = ArrayHelper::map($rec_language, 'table_field','content');
                $goods->name = isset($mapArr['name'])?$mapArr['name']:$goods->name;
                $goods->description = isset($mapArr['description'])?$mapArr['description']:$goods->description;
            }
        }

        return $data;
    }


    /**
     * 获取收藏的商品id列表
     * @param int $uid
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function _get_favorite($uid=0){
        $data = Goods::find()
            ->join('RIGHT JOIN','goods_favorite as f','f.goods_id = goods.id')
            ->where(['f.uid'=>$uid])
            ->all();
        if($data){
            $data = ArrayHelper::getColumn($data,'id');
        }
        return $data;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoodsPeopleItems() {
        return $this->hasMany(GoodsPeopleItem::className(), ['item_id' => 'id']);
    }

    public function getSku() {
        return $this->hasMany(GoodsSku::className(), ['goods_id' => 'id'])
            ->where(['status' => '1']);
    }

    public function getLanguage($lang = LANG_SET) {
        return $this->hasMany(GoodsLanguage::className(), ['table_id' => 'id'])
            ->where(['table_field' => ['name', 'description', 'spec_description'], 'table_name' => 'goods', 'language' => $lang]);
    }

    public function getPhoto() {
        return $this->hasMany(GoodsPhoto::className(), ['goods_id' => 'id'])->orderBy(['place' => SORT_ASC]);
    }

    public function getCategory() {
        return $this->hasOne(GoodsCategory::className(), ['id' => 'category_id']);
    }

    public function getProperties() {
        return $this->hasOne(GoodsProperties::className(), ['goods_id' => 'id']);
    }

    public function getBrand() {
        return $this->hasOne(GoodsBrand::className(), ['brand_id' => 'brand_id']);
    }
}
