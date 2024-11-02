<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'response' => [
            'class' => \yii\web\Response::class,
            'format' => \yii\web\Response::FORMAT_JSON,
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                $data = is_array($response->data) ? $response->data : [$response->data];
                $exception = Yii::$app->errorHandler->exception;

                if ($response->isSuccessful) {
                    $response->data = \app\common\Response::getSuccessResponse($data);
                } elseif (!is_null($exception)) {
                    if ($exception instanceof \app\common\DomainException) {
                        $response->data = \app\common\Response::getErrorResponse($exception->getDomainError());
                    } else {
                        $response->data = \app\common\Response::getErrorResponse([
                            'code' => \app\enums\DomainErrors::CUSTOM_ERROR_CODE,
                            'message' => $exception->getMessage(),
                        ]);
                    }
                } else {
                    \app\common\Response::getErrorResponse([
                        'code' => \app\enums\DomainErrors::CUSTOM_ERROR_CODE,
                        'message' => 'Unknown error',
                    ]);
                }
            },
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'vm8fvHB2cdcQfgPAXRtnoC07MiocUs',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => \app\models\Account::class,
            'enableAutoLogin' => true,
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
