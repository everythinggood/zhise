<?php
/**
 * Created by PhpStorm.
 * User: ycy
 * Date: 8/20/18
 * Time: 11:08 PM
 */

$key = $argv[1];
$cpmUrl = $argv[2];

echo "key=".$key.PHP_EOL;
echo "cpmUrl=".$cpmUrl.PHP_EOL;

if(!$key) throw new Exception("you need redis key to set!");
if(!$cpmUrl) throw new Exception("you need cpm url to search!");

require __DIR__ . '/../vendor/autoload.php';

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

/** @var Redis $redis */
$redis = $app->getContainer()['redis'];

$handle = fopen(__DIR__.'/cpm_filter.csv','r');

$machineIDs = [];

while($data = fgetcsv($handle)){
    if($data[1] == $cpmUrl){
        $machineIDs[] = strtolower($data[0]);
    }
}

fclose($handle);

if(count($machineIDs) < 1) throw new Exception("machineIDS is null!");

function isMatch(array $machineIDs,$search){
    foreach ($machineIDs as $machineID){
        if(strpos($search,$machineID) > 0){
            return true;
        }
    }
    return false;
}

if(!$redis->exists($key)) throw new Exception("$key is not exist!");

$count = $redis->lLen($key);

$result = 0;
for ($i = 0; $i < $count; $i++){
    $value = $redis->lIndex($key,$i);
    if(isMatch($machineIDs,$value)){
        $result ++;
    }
}

$redis->close();

echo $result.PHP_EOL;

