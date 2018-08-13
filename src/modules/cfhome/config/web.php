<?php
define('__STATIC__',CDN_URL.'/static');
define('__JS__',CDN_URL.'/cfhome/js');
define('__IMG__',CDN_URL.'/cfhome/images');
define('__CSS__',CDN_URL.'/cfhome/css');
define('__AVATARS__',CDN_URL.'/Common/images/UserAvatar');
define('FB_APPID','1310114405711586');
define('GG_APPID','1056983584820-14nt5k2fs8rjmupraoqbmnihen3bdm23.apps.googleusercontent.com');

if(YII_ENV == 'dev'){
    define('TID',33);
    define('EVENTS_ID',42);
    define('ANNOUNCEMENT_ID',43);
    define('GUIDE_ID',47);
    define('STRATEGY_ID',49);
    define('SHOP_ID', 1);
}elseif(YII_ENV == 'qa'){
    define('TID',32);
    define('EVENTS_ID',34);
    define('ANNOUNCEMENT_ID',35);
    define('GUIDE_ID',36);
    define('STRATEGY_ID',41);
    define('SHOP_ID', 1);
}else{
    define('TID', 29);
    define('EVENTS_ID', 40);
    define('ANNOUNCEMENT_ID', 39);
    define('GUIDE_ID', 38);
    define('STRATEGY_ID', 37);
    define('SHOP_ID', 1);
}


$_config = [
     'id'                  => 'cfhome',
    'language'=>'en-us',
    'components'          => [
        'i18n' => [
            'class'        => 'yii\i18n\I18N',
            'translations' => [
                '*' => [
                    'class'    => 'yii\i18n\PhpMessageSource',
                    'basePath' => dirname(__DIR__).'/languages/',
                    'forceTranslation'=>true
                ],
                'cf' => [
                    'class'    => 'yii\i18n\PhpMessageSource',
                    'basePath' => dirname(__DIR__).'/cf/languages/',
                    'forceTranslation'=>true,
                    'fileMap' => [
                        'cf' => 'common.php'
                    ],
                ],
            ],
        ],
    ],
];

return $_config;