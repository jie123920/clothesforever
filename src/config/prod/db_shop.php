<?php

return [
    'class' => 'yii\db\Connection',
    'tablePrefix' => '',

    // common configuration for masters
    'masterConfig' => [
        'username' => 'mutantboxdbm',
        'password' => 'ei#9yR8W*3dDf4',
        'charset' => 'utf8',
    ],

    // list of master configurations
    'masters' => [
        ['dsn' => 'mysql:host=23.236.122.13;dbname=pro_mutantbox_business'],
    ],

    // common configuration for slaves
    'slaveConfig' => [
        'username' => 'mutantboxdbm',
        'password' => 'ei#9yR8W*3dDf4',
        'charset' => 'utf8',
    ],

    // list of slave configurations
    'slaves' => [
        ['dsn' => 'mysql:host=23.236.122.14;dbname=pro_mutantbox_business'],
    ],

    // 'serverStatusCache' => 'file_cache',
];
