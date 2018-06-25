<?php
/**
 * Created by PhpStorm.
 * User: ycy
 * Date: 4/3/18
 * Time: 10:57 AM
 */

namespace Container\View;


use Psr\Http\Message\ResponseInterface;

class JsonView extends \Slim\Views\JsonView
{


    public function renderSuccess(ResponseInterface $response,array $data){
        $res['data'] = $data;
        $res['errMsg'] = "request:ok!";
        $res['code'] = 0;

        return $this->render($response,$res);
    }

    public function renderError(ResponseInterface $response,$errMsg,$code,array $data = null){
        $res['data'] = $data;
        $res['errMsg'] = $errMsg;
        $res['code'] = $code;

        return $this->render($response,$res);
    }

}