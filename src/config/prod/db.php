<?php

return [
    'class' => 'yii\db\Connection',
    'tablePrefix' => 'ww2_',

    // common configuration for masters
    'masterConfig' => [
        'username' => 'pro_cfweb_dbm',
        'password' => 'T1D7zr0ZH3erqZG',
        'charset' => 'utf8',
    ],

    // list of master configurations
    'masters' => [
        // ['dsn' => 'mysql:host=172.31.14.130;dbname=pro_cf_web'],
        ['dsn' => 'mysql:host=69.28.60.54;dbname=pro_cf_web'],
    ],

    // common configuration for slaves
    'slaveConfig' => [
        'username' => 'pro_cfweb_dbm',
        'password' => 'T1D7zr0ZH3erqZG',
        'charset' => 'utf8',
    ],

    // list of slave configurations
    'slaves' => [
        // ['dsn' => 'mysql:host=172.31.5.79;dbname=pro_cf_web'],
        ['dsn' => 'mysql:host=69.28.60.54;dbname=pro_cf_web'],
    ],

   // 'serverStatusCache' => 'file_cache',
];
