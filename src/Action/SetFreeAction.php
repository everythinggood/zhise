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

        $date = (new \DateTime())->format('Y-m-d');

        $key = $wxopenid."_$date";

        if($this->redis->exists($key)){
            return $this->view->renderError($response,"wxopenid is exists!",ExceptionCode::NAME_EXIST_EXCEPTION);
        }

        $this->redis->set($key,$machine);

        return $this->view->renderSuccess($response,[
            'set'=>true,
            'wxopenid'=>$wxopenid,
            'machine'=>$machine
        ]);

    }

}