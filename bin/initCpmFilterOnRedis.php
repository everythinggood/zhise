<?php
/**
 * Created by PhpStorm.
 * User: ycy
 * Date: 6/25/18
 * Time: 11:16 AM
 */

require __DIR__ . '/../vendor/autoload.php';

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

$json = file_get_contents(__DIR__.'/cpm_filter.json');

$cpmFilter = json_decode($json,true);
/** @var Redis $redis */
$redis = $app->getContainer()['redis'];

$redis = $redis->multi();

foreach ($cpmFilter as $machineId=>$url){
    $redis->hSet(\Container\View\RedisKey::KEY_CPM_FILTER,$machineId,$url);
}

$result = $redis->exec();

file_put_contents(__DIR__.'/cpm_filter.log',var_export($result));

