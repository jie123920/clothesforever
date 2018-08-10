<?php
//访问协议
define('PROTOCOL', $_SERVER['SERVER_PORT']==443 ? 'https' : 'http');

//定义回调URL通用的URL
define('URL_CALLBACK', PROTOCOL.'://testwww.movemama.com/login/');
//主域
define('DOMAIN', 'clothesforever.com');

define('CONNECTURL', 'http://testplay.clothesforever.com/public');
define('CONNECTURL2', PROTOCOL.'://testplay.clothesforever.com/public');
//环境配置
define('ENVUC', 'qa');

//UcenterUrl配置
$tmphosts = explode('.', $_SERVER['HTTP_HOST']);
unset($tmphosts[0]);
$tmphosts = implode('.', $tmphosts);
define('UCENTER_URL', PROTOCOL.'://testucenter.' . $tmphosts . '/');
unset($tmphosts);
//时间戳
define('NOWTIME', time());
//版本号
define('VERSION', '20160622');

//所有日志的路径
define('LOG_PATH', "/data/logs/");

//图片上传根目录
define('UPLOAD_IMAGE_FILE_URL', '/Uploads/images/');
define('UPLOAD_IMAGE_FILE', dirname(dirname(__DIR__)) .'/web'. UPLOAD_IMAGE_FILE_URL);
//构建目录名称
define('GULP', 'dist');
define('CDN_URL', '//testcdn.movemama.com/16/00');
define('UPLOAD_CDN_URL', '//testcdn.movemama.com/00/03/uploads');
define('__SELF__', strtolower(strip_tags($_SERVER['REQUEST_URI'])));
define('DEFAULT_AVATAR','https://cdn-image.mutantbox.com/201709/26012560f9a806190a05f6d634bc22b1.png');
define('LC_SHOP_API_URL',PROTOCOL . '://testwww.lovecrunch.com/shop/');

return [
    'COOKIE_DOMAIN' => DOMAIN,
    'adminEmail'          => 'admin@mutantbox.com',
    //返回数据格式
    'result'              => ['code' => 0, 'error' => [], 'data' => []],
    'pay'                 => PROTOCOL.'://testpay.movemama.com',
    'play'                => PROTOCOL.'://testplay.movemama.com',
    'LOGURL2'             => '163.44.165.46',
    'LOGPORT2'            => '5515',
    'LOGURL' => '133.130.90.180',
    'LOGPORT' => '8889',
    //url 设置
    'MY_URL'=>array(
        'PAY'  => PROTOCOL.'://testpay.movemama.com',
        'PAYS' => 'https://testpay.movemama.com',
        'WEB'=>PROTOCOL.'://testwww.clothesforever.com/',
        'SAPI' => 'http://sapi.movemama.com',
        'OPS' => 'http://ops.movemama.com',
        'UCENTER' => PROTOCOL.'://testucenter.clothesforever.com',
        'EMAIL'=>'http://127.0.0.1:18000',
        'ShopPay' => PROTOCOL.'://testpay.clothesforever.com',
        'ShopPay_2' => PROTOCOL.'://testpay.lovecrunch.com',
        'SHOP'=> PROTOCOL.'://testshop.clothesforever.com',
        'CF'=> PROTOCOL.'://testwww.clothesforever.com',
        'CF-FORUM'=> 'http://testforum.clothesforever.com',
        'MUTANTBOX'=>PROTOCOL.'://testwww.movemama.com',
        'M_SHOP_URL'=> PROTOCOL . '://testm.clothesforever.com',
        'LC_SHOP_URL'=> PROTOCOL . '://testwww.lovecrunch.com',
    ),

    'GAME_CONFIG' => array(
        '7' => array(),
    ),
    'allowed_server_type' => [
        31 => 31, // 正式服
        // 41 => 41, // 永测服
        // 42 => 42, // 外网测试服
        // 43 => 43, // 内网测试服
        // 44 => 44, // QA测试服
    ],
    'PLATFORM_NUM_GW'    => 3,
    'PLATFORM_NUM_FB'    => 4,


    'TOKEN'                 => array(
        'KEY'      => 'uow)*^$@!#%&456kj',
        'ucentkey' => 'cf1FleJPllq9uZj9Juje2',
        'PASSKEY'  => 'Liberators123!@#',
        'email_token'=>'c227a43454a2fcac3fbb0d9ce8d8cfa7',
        'shop' => 'rZ2Xj7Q77Tv1lKvZ',
        'queue' => '4ZWrfeG2FEl6Llzu',
    ),
    /* 文章封面原图片上传相关配置 */
    'ARTICLEPICTURE_UPLOAD' => array(
        'mimes' => '', //允许上传的文件MiMe类型
        'maxSize' => 2 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
        'exts' => 'jpg,gif,png,jpeg', //允许上传的文件后缀
        'autoSub' => true, //自动子目录保存文件
        'subName' => array('date', 'Ym'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/images/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt' => '', //文件保存后缀，空则使用原后缀
        'replace' => false, //存在同名是否覆盖
        'hash' => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ),

    'RSYNC_CDN_ADDRESS' => array(
        '52.192.55.250::uploads.testcdn.movemama.com',
    ),
    'language' => [
        'en-us' => '1',
        'fr-fr' => '2',
        'de-de' => '3',
        'es-es' => '4',
        //         '5' => 'ch-ch',
        'pt-pt' => '6',
        'ar-ar' => '7',
        'el-el' => '8',
        'tr-tr' => '9',
        'pl-pl' => '10',
        //         '11' => 'cs-cs',
    //         '12' => 'it-it',
    //         '13' => 'hu-hu',
        'ro-ro' => '14',
    ],


    'image_server_app_id'     => '782937352627475',
    'image_server_secret_key' => 'c227a43454a2fcac3fbb0d9ce8d8cfa7',
    'image_server_host'       => 'testimage.movemama.com',
    'image_server_version'    => 'v1',
];
