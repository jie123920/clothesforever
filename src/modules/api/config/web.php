<?php
$_config = [
     'id'                  => 'api',
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
            ],
        ],
    ],
];

return $_config;