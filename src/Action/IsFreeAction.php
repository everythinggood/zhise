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

    public function __construct(ContainerInterface $container)
    {
        $this->redis = $container['redis'];
        $this->view = $container['view'];
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        /** @var Request $request */
        /** @var Response  $response */
        $wxopenid = $request->getParam('wxopenid');
        $machine = $request->getParam('machine');

        if(!$wxopenid) return $this->view->renderError($response,"wxopenid is not found!",ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);
        if(!$machine) return $this->view->renderError($response,'machine is not found!',ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);

        if($this->redis->exists($wxopenid)){
            return $this->view->renderError($response,"wxopenid is exist!",ExceptionCode::NAME_EXIST_EXCEPTION);
        }

        return $this->view->renderSuccess($response,[
           'free'=>true
        ]);
    }

}