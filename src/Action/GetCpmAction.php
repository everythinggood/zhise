<?php
/**
 * Created by PhpStorm.
 * User: ycy
 * Date: 4/3/18
 * Time: 12:17 AM
 */

namespace Action;


use Container\View\JsonView;
use Contract\ExceptionCode;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class GetCpmAction implements ActionInterface
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

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return mixed|static
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        /** @var Request $request */
        /** @var Response  $response */
        $wxopenid = $request->getParam('wxopenid');
        $machine = $request->getParam('machine');
        $tag = $request->getParam('tag');

        if(!$wxopenid) return $this->view->renderError($response,"wxopenid is not found!",ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);
        if(!$machine) return $this->view->renderError($response,'machine is not found!',ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);

        $url = $this->redis->hGet('cpm','url');

        $baseUrl = $this->redis->hGet('cpm','baseUrl');

        if($this->redis->hExists('cpm',$tag)){
            return $this->view->renderError($response,"cpm can not set on [$tag]",ExceptionCode::NAME_EXIST_EXCEPTION,[
                'url'=>$baseUrl
            ]);
        }

        return $response->withJson([
            'exists'=>false,
            'url'=>$url
        ]);
    }

}