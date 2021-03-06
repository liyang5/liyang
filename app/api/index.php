<?php
date_default_timezone_set('America/Los_Angeles');

require_once 'common/Config.php';
\common\Config::registerAutoloader();

$app = new \common\Rest();
$config = new \common\Config($app->getMode());
\common\DB::config($config->get('database'));

$jieqi = new \app\Jieqi();
$app->get('/jies/:time', function ($time) use ($app, $jieqi) {
    return $jieqi->findJie($time);
});

$name = new \app\Name();
$app->post('/chinese_names', function () use ($app, $name) {
    return $name->findChineseName($app->request()->getBody());
});

$app->get('/users/current', function () use ($app) {
    get("user", $app);
});
$app->post('/users/login', function () use ($app) {
    $entity = $app->request()->getBody();
    if ($entity['userName'] == 'ktong') {
        $app->halt(401, '{"_message": {"forUser": "login failed."}}');
    }
    $app->setCookie('is_logon', 'true', '1 hour');
    return array("status" => "success");
});
$app->post('/users/logout', function () use ($app) {
    $app->deleteCookie('is_logon');
});

$app->run();

function get($name, $app)
{
    if ('true' != $app->getCookie('is_logon')) {
        $app->halt(401);
    }
    try {
        $app->render("$name.json");
    } catch (RuntimeException $e) {
        $app->notFound();
    }
}

function post($app)
{
    if ('true' != $app->getCookie('is_logon')) {
        $app->halt(401);
    }
    $entity = $app->request()->getBody();
    $entity['id'] = 99;
    return $entity;
}

function put($app)
{
    if ('true' != $app->getCookie('is_logon')) {
        $app->halt(401);
    }
    return $app->request()->getBody();
}
