<?php
/**
 * Created by PhpStorm.
 * User: ycy
 * Date: 4/3/18
 * Time: 12:11 AM
 */

namespace Action;


use Container\View\JsonView;
use Contract\ExceptionCode;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class SetFreeAction implements ActionInterface
{
    /**
     * @var \Redis
     */
    private $redis;
    /**
     * @var JsonView
     */
    private $view;
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->redis = $container['redis'];
        $this->view = $container['view'];
        $this->logger = $container['logger'];
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        /** @var Request $request */
        /** @var Response  $response */
        $wxOpenId = $request->getParam('wxOpenId');
        $machine = $request->getParam('machineCode');
        $adCode = $request->getParam('adCode');

        if(!$wxOpenId) return $this->view->renderError($response,"wxOpenId is not found!",ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);
        if(!$machine) return $this->view->renderError($response,'machineCode is not found!',ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);

        $this->logger->addInfo('SetFreeAction-request',$request->getParams());

        $date = (new \DateTime())->format('Y-m-d');

        $adKey = "ad_$date";

        if($this->redis->hExists($adKey,$wxOpenId)){
            return $this->view->renderError($response,"wxOpenId is exists!",ExceptionCode::NAME_EXIST_EXCEPTION);
        }

        $dateTime = (new \DateTime())->format('Y-m-d H:i:s');

        $this->redis->hSet($adKey,$wxOpenId,$machine."_$dateTime");

        if($adCode && $this->redis->hExists('ad',$adCode)){
            $this->redis->hSet($adCode.'_user',$wxOpenId,$machine."_$dateTime");
        }

        $this->redis->close();

        return $this->view->renderSuccess($response,[
            'set'=>true,
            'wxOpenId'=>$wxOpenId,
            'machine'=>$machine
        ]);

    }

}