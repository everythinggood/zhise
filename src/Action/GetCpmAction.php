<?php
/**
 * Created by PhpStorm.
 * User: ycy
 * Date: 4/3/18
 * Time: 12:17 AM
 */

namespace Action;


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

    public function __construct(ContainerInterface $container)
    {
        $this->redis = $container['redis'];
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

        if(!$wxopenid) throw  new \Exception("wxopenid is not found!");
        if(!$machine) throw new \Exception('machine is not found!');

        $url = $this->redis->hGet('cpm','url');

        $baseUrl = $this->redis->hGet('cpm','baseUrl');

        if($this->redis->hExists('cpm',$tag)){
            return $response->withJson([
               'exists'=>true,
               'url'=>$baseUrl
            ]);
        }

        return $response->withJson([
            'exists'=>false,
            'url'=>$url
        ]);
    }

}