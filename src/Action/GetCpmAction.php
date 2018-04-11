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
use Monolog\Logger;
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
        $machine = $request->getParam('machinecode');
        $tag = $request->getParam('tag');

        if(!$wxopenid) return $this->view->renderError($response,"wxopenid is not found!",ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);
        if(!$machine) return $this->view->renderError($response,'machinecode is not found!',ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);
//        if(!$tag) return $this->view->renderError($response,'tag is not found!',ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);

        $url = $this->redis->hGet('cpm','url');

        $baseUrl = $this->redis->hGet('cpm','baseUrl');

        $this->logger->addInfo("getCpmAction-request",[$wxopenid,$machine,$tag,$url,$baseUrl]);

        if(!$this->redis->hExists('cpm',$tag)){
            return $this->view->renderError($response,"cpm can not set on [$tag]",ExceptionCode::NAME_NOT_EXIST_EXCEPTION,[
                'exist'=>false,
                'url'=>$baseUrl
            ]);
        }

        $addRequestParam = $this->autoAcountCpm($wxopenid,$machine);

        $this->logger->addInfo('getCpmAction-response',[$wxopenid,$machine,$tag,$url,$baseUrl,$addRequestParam]);

        return $this->view->renderSuccess($response,[
            "addRequestParam"=>$addRequestParam,
            'exist'=>true,
            'url'=>$url
        ]);
    }

    protected function autoAcountCpm($wxopenid,$machine){
        $listName = "cpm".(new \DateTime())->format('Y-m-d');
        return $this->redis->lPush($listName,join('_',[$wxopenid,$machine]));
    }

}