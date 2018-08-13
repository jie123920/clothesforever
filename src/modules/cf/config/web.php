<?php
define('GOOGLE_APP_URL', 'https://play.google.com/store/apps/details?id=com.mutantbox.clothesforever.android');
define('APPLE_APP_URL', 'https://itunes.apple.com/app/clothes-forever-fashion-styling-shopping-game/id1187777927?mt=8');

define('__STATIC__', CDN_URL . '/static');
define('__JS__', CDN_URL . '/cf/js');
define('__IMG__', CDN_URL . '/cf/images');
define('__CSS__', CDN_URL . '/cf/css');
define('__AVATARS__', CDN_URL . '/Common/images/UserAvatar');
define('__LAYER__', CDN_URL . '/cf/layer');
if (YII_ENV == 'dev') {
    define('TID', 33);
    define('EVENTS_ID', 42);
    define('ANNOUNCEMENT_ID', 43);
    define('GUIDE_ID', 47);
    define('STRATEGY_ID', 49);
    define('SHOP_ID', 1);
    define('MAX_CART_COUNT_ITEM_NUMBER', 50);
    define('MAX_CART_ITEM_NUMBER', 30);
} elseif (YII_ENV == 'qa') {
    define('TID', 32);
    define('EVENTS_ID', 34);
    define('ANNOUNCEMENT_ID', 35);
    define('GUIDE_ID', 36);
    define('STRATEGY_ID', 41);
    define('SHOP_ID', 1);
    define('MAX_CART_COUNT_ITEM_NUMBER', 50);
    define('MAX_CART_ITEM_NUMBER', 30);

} else {
    define('TID', 29);
    define('EVENTS_ID', 40);
    define('ANNOUNCEMENT_ID', 39);
    define('GUIDE_ID', 38);
    define('STRATEGY_ID', 37);
    define('SHOP_ID', 1);
    define('MAX_CART_COUNT_ITEM_NUMBER', 50);
    define('MAX_CART_ITEM_NUMBER', 30);

}
$_config = [
    'id'         => 'cf',
    'language'   => 'en-us',
    'components' => [
        'i18n' => [
            'class'        => 'yii\i18n\I18N',
            'translations' => [
                '*' => [
                    'class'            => 'yii\i18n\PhpMessageSource',
                    'basePath'         => dirname(__DIR__) . '/languages/',
                    'forceTranslation' => true,
                ],
                'shop' => [
                    'class'            => 'yii\i18n\PhpMessageSource',
                    'basePath'         => dirname(dirname(__DIR__)) . '/shop/languages/',
                    'fileMap' => [
                        'shop' => 'common.php',
                    ],
                    'forceTranslation' => true,
                ],
            ],
        ],
    ],
];

return $_config;
