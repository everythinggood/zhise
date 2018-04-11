<?php
/**
 * Created by PhpStorm.
 * User: ycy
 * Date: 4/2/18
 * Time: 11:27 PM
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

class IsFreeAction implements ActionInterface
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
        $wxopenid = $request->getParam('wxopenid');
        $machine = $request->getParam('machinecode');

        if(!$wxopenid) return $this->view->renderError($response,"wxopenid is not found!",ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);
        if(!$machine) return $this->view->renderError($response,'machinecode is not found!',ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);

        $this->logger->addInfo('isFreeAction-request',[$wxopenid,$machine]);

        $date = (new \DateTime())->format('Y-m-d');

        $key = $wxopenid."_$date";

        if($this->redis->exists($key)){
            return $this->view->renderError($response,"wxopenid is exist!",ExceptionCode::NAME_EXIST_EXCEPTION);
        }

        return $this->view->renderSuccess($response,[
           'free'=>true
        ]);
    }

}