<?php
$params = require(__DIR__ . '/' . YII_ENV . '/params.php');
//引入自定义类库
Yii::$classMap['Connect'] = '@app/Library/Connect.php';
Yii::$classMap['Errors'] = '@app/Library/Errors.php';
Yii::$classMap['Userinfo'] = '@app/Library/Userinfo.php';


$params['lang'] = [
    'zh-cn'=>'简体中文',
    'en-us'=>'English',//英语
    'fr-fr'=>'Français',//法语
    'de-de'=>'Deutsch',//德语
    'es-es'=>'Español',//西班牙语
    'pt-pt'=>'Português',//葡萄牙语
    'ar-ar'=>'العَرَبِيَّة‎‎',//阿拉伯语
    'tr-tr'=>'Türkçe',//土耳其语
    'pl-pl'=>'Polski',//波兰语
    'ro-ro'=>'Română',//罗马尼亚语
    'el-el'=>'Ελληνικά',//希腊语
    'it-it'=>'Italiano',//意大利语
    'cs-cs'=>'Čeština',//捷克语
    'hu-hu'=>'Magyar',//匈牙利语
];
//FaceBook配置
$params['THINK_SDK_FACEBOOK'] = array(
    'APP_KEY' => '714278052039214',
    'APP_SECRET' => '3c455733f51025630be718fce9ac963d',
    'CALLBACK' => URL_CALLBACK . 'facebook',
);
//Google配置
$params['THINK_SDK_GOOGLE'] = array(
    'APP_KEY' => '455615275123-gnkurafbvjr751fioggsujmrli55ltu8.apps.googleusercontent.com',
    'APP_SECRET' => '1SDu1DEcRhFPeLSsU8zg6dI_',
    'CALLBACK' => PROTOCOL.'://www.mutantbox.com',
);

//FaceBook配置
$params['THINK_SDK_FACEBOOK'] = array(
    'APP_KEY' => '1310114405711586',
    'APP_SECRET' => 'cebdfb46f1ff1ca01252aff217fe367f',
    'CALLBACK' => URL_CALLBACK . 'facebook',
);
//Google配置
$params['THINK_SDK_GOOGLE'] = array(
    'APP_KEY' => '1056983584820-14nt5k2fs8rjmupraoqbmnihen3bdm23.apps.googleusercontent.com',
    'APP_SECRET' => 'D22_1pgoAF_n_aA-qM9E2KsK',
    'CALLBACK' => PROTOCOL.'://www.clothesforever.com',
);

$config = [
    'id'=>'app',
    'basePath'=>dirname(__DIR__),
    'params' => $params,
    'defaultRoute' => BIND_MODULE.'/index/index',
];

$config['modules'] = [
    'cfhome'   => [
        'class' => 'app\modules\cfhome\Module',
    ],
    'cf'   => [
        'class' => 'app\modules\cf\Module',
    ],
    'api'   => [
        'class' => 'app\modules\api\Module',
    ],
    'debug' => [
        'class'      => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ],
];
$config['bootstrap'] = ['log', 'debug'];

$config['components'] = [
    'errorHandler'=>array(
        'errorAction'=>'/cfhome/common/empty',
    ),
    'mailer' => [
        'class' => 'yii\swiftmailer\Mailer',
        'viewPath' => '',
        'transport' => [
            'class' => 'Swift_SmtpTransport',
            'host' => 'smtp.mailgun.org',
            'port' => 25,
            'encryption' => 'tls',
            'username' => 'postmaster@mutantboxmail.com',
            'password' => '233fb47265250cb7d8356f2089941433',
        ],
        'messageConfig' => [
            'charset' => 'utf-8',
            'from' => ['noreply@mutantboxmail.com' => 'MutantBox']
        ],
    ],
    'user' => [
        'identityClass' => 'app\models\User',
        'enableAutoLogin' => false,
    ],
    'log' => [
        'traceLevel' => YII_DEBUG ? 3 : 0,
        'targets' => [
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['error'],
                'logVars'=>[]
            ],
        ],
    ],

    'cache'        => [
        'class' => 'yii\caching\FileCache',
    ],

    'urlManager'       => [
        'class'           => 'yii\web\UrlManager',
        'enablePrettyUrl' => true,
        'showScriptName'  => false,
        'cache'           => false,
        'rules'           => [
            '/contact'=>'/cfhome/company/contact',
            '/news'=>'/'.BIND_MODULE.'/article/index',
            '/news/<id:\d+>/<title:[\s|\S]*>' => '/'.BIND_MODULE.'/article/article',
            '/support'=>'/cfhome/support/index',
            '/community'=>'/cfhome/community/index',
            '/termsofuse' => '/cfhome/article/termsofuse',
            '/privacypolicy' => '/cfhome/article/privacypolicy',
            '/deliverypolicy' => '/cfhome/article/deliverypolicy',
            '/guide'=>'/cfhome/article/guide',
            '/404'=>'/cfhome/common/empty',
            '/ucenter/<action>'=>'/cfhome/ucenter/<action>',
            '/usercenter/<action>'=>'/cfhome/usercenter/<action>',
            '/faq/<action>'=>'/cfhome/faq/<action>',
            '/support/<action>'=>'/cfhome/support/<action>',
            '/company'=>'/cfhome/company',
            '/game/<action>'=>'/cfhome/game/<action>',
            '/play/<action>'=>'/cfhome/play/<action>',
            '/checkserver/<action>'=>'/cfhome/checkserver/<action>',
            '/game'=>'/cf/article/gameinfo',
            '/login'=>'/cfhome/login/login',
            '/register'=>'/cfhome/login/register',
            '/login/<action>' => '/cfhome/login/<action>',
            '<controller>/<action>' => BIND_MODULE.'/<controller>/<action>',
            '<controller>/<action>\/\*' => BIND_MODULE.'/<controller>/<action>',
            '/api/v1/<controller>/<action>' => '/shop/<controller>/<action>',
            '/'=> (strstr(ALISA,'shop'))? '/'.BIND_MODULE.'/shop/index':'/'.BIND_MODULE.'/index/index',//SHOP
            '/style'=> (strstr(ALISA,'shop'))? '/'.BIND_MODULE.'/shop/style':'/'.BIND_MODULE.'/index/index',
            '/my-favorite'=> (strstr(ALISA,'shop'))? '/'.BIND_MODULE.'/shop/my-favorite':'/'.BIND_MODULE.'/index/index',
            '/download'=>'/cf/index/download',
            '/shop'=>'/cf/index/shop'
        ],
    ],
    'request' => [
        'class'               => 'yii\web\Request',
        'cookieValidationKey' => 'V1sUo0zLiK-n42OZxsYO1vRa8Wd4ks6x',
    ],
    'session' => [
        'cookieParams' => ['domain' => '.' . DOMAIN],
    ],
    'db' => require(__DIR__  .  '/' . YII_ENV .  '/db.php'),
    'db_shop' => require(__DIR__  .  '/' . YII_ENV .  '/db_shop.php'),
    'dbpay'=>require(__DIR__  .  '/' . YII_ENV .  '/dbpay.php'),
];
if (YII_ENV_DEV) {
//     configuration adjustments for 'dev' environment
   $config['bootstrap'][] = 'debug';
   $config['modules']['debug'] = [
       'class' => 'yii\debug\Module',
   ];

   $config['bootstrap'][] = 'gii';
   $config['modules']['gii'] = [
       'class' => 'yii\gii\Module',
   ];
}

return $config;
