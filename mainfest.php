<?php
return [
	'id'=> 'comment',
	'migrationPath' => '@vendor/yuncms/comment/migrations',
    'translations' => [
        'yuncms/attention' => [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@vendor/yuncms/comment/messages',
        ],
    ],
	'backend' => [
	    "class": "yuncms\\comment\\backend\\Module"
	],
    'frontend' => [
        "class": "yuncms\\comment\\frontend\\Module"
    ],
];