<?php
/**
 * Created by PhpStorm.
 * User: ycy
 * Date: 4/12/18
 * Time: 3:43 PM
 */

namespace Action;


use Container\View\JsonView;
use Contract\ExceptionCode;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;

class GetMachineAction implements ActionInterface
{
    /**
     * @var JsonView
     */
    private $view;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var \Redis
     */
    private $redis;

    public function __construct(ContainerInterface $container)
    {
        $this->redis = $container['redis'];
        $this->view = $container['view'];
        $this->logger = $container['logger'];
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        /**
         * @var Request $request
         */
        $wxOpenId = $request->getParam('wxOpenId');

        if(!$wxOpenId) return $this->view->renderError($response,'require [wxOpenId] param!',ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);

        $this->logger->addInfo('getMachineAction-request',$request->getParams());

        $date = (new \DateTime())->format('Y-m-d');

        $machine = $this->redis->hGet("ad_$date"."_scan",$wxOpenId);

        $this->redis->close();

        return $this->view->renderSuccess($response,[
           'machine'=>$machine?$machine:null,
            'adCode'=>$machine?'10010':null
        ]);

    }

}