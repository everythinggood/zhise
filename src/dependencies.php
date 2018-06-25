<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

$container['redis'] = function(){
  $redis = new Redis();
  $redis->connect("redis",6379);
//  $redis->auth("zhise");
  return $redis;
};

$container['view'] = function (){
    return new \Container\View\JsonView();
};

//errorHandler
//$container['errorHandler'] = function ($c) {
//    return function ($request, $response, $exception) use ($c) {
//        /** @var \Monolog\Logger $logger */
//        $logger = $c['logger'];
//        $logger->error(strval($exception), (array)$request);
//        return $c['response']->withStatus(500)
//            ->withHeader('Content-Type', 'text/html')
//            ->write('Something went wrong!');
//    };
//};
//
//$container['phpErrorHandler'] = function ($c) {
//    return function ($request, $response, $error) use ($c) {
//        /** @var \Monolog\Logger $logger */
//        $logger = $c['logger'];
//        $logger->error(strval($error), (array)$request);
//        return $c['response']
//            ->withStatus(500)
//            ->withHeader('Content-Type', 'text/html')
//            ->write('Something went wrong!');
//    };
//};
