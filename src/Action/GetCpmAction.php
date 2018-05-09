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
        $wxOpenId = $request->getParam('wxOpenId');
        $machine = $request->getParam('machineCode');
        $tag = $request->getParam('tag');

        if(!$wxOpenId) return $this->view->renderError($response,"wxOpenId is not found!",ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);
        if(!$machine) return $this->view->renderError($response,'machineCode is not found!',ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);
//        if(!$tag) return $this->view->renderError($response,'tag is not found!',ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);

        $this->logger->addInfo('getCpmAction-request',$request->getParams());

        $url = $this->redis->hGet('cpm','url');

        $baseUrl = $this->redis->hGet('cpm','baseurl');

        $this->logger->addInfo("getCpmAction-request",[$wxOpenId,$machine,$tag,$url,$baseUrl]);

        if($this->redis->sIsMember('cpmignore',$machine)){

            return $this->view->renderSuccess($response,[
                'cpmIgnore'=>true,
                'url'=>$this->redis->hGet('cpm','baseurl')
            ]);
        }

        if(!$this->redis->hExists('cpm',$tag)){
            return $this->view->renderError($response,"cpm can not set on [$tag]",ExceptionCode::NAME_NOT_EXIST_EXCEPTION,[
                'exist'=>false,
                'url'=>$baseUrl
            ]);
        }

        $addRequestParam = $this->autoAccountCpm($wxOpenId,$machine);

        $this->logger->addInfo('getCpmAction-response',[$wxOpenId,$machine,$tag,$url,$baseUrl,$addRequestParam]);

        $this->redis->close();

        return $this->view->renderSuccess($response,[
            "recordCpm"=>$addRequestParam,
            'exist'=>true,
            'url'=>$url
        ]);
    }

    protected function autoAccountCpm($wxopenid, $machine){
        $listName = "cpm".(new \DateTime())->format('Y-m-d');
        return $this->redis->lPush($listName,join('_',[$wxopenid,$machine]));
    }

}