<?php

/** @var \Slim\App $app */

$app->group('/api',function (){


    $this->post('/is/free',\Action\IsFreeAction::class);
    $this->post('/set/free',\Action\SetFreeAction::class);
    $this->post('/get/cpm',\Action\GetCpmAction::class);
    $this->post('/get/machine',\Action\GetMachineAction::class);

})->add(

    $app->getContainer()['basic_auth_middleware']
);
