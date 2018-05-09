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
        /** @var Response $response */
        $wxOpenId = $request->getParam('wxOpenId');
        $machine = $request->getParam('machineCode');

        if (!$wxOpenId) return $this->view->renderError($response, "wxOpenId is not found!", ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);
        if (!$machine) return $this->view->renderError($response, 'machineCode is not found!', ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);

        $this->logger->addInfo('isFreeAction-request', $request->getParams());

        $date = (new \DateTime())->format('Y-m-d');

        $adKey = "ad_$date";

        //今天有没有免费领取机会
        if ($this->redis->hExists($adKey,$wxOpenId)) {
            return $this->view->renderError($response, "wxOpenId is exist!", ExceptionCode::NAME_EXIST_EXCEPTION);
        }

        //增加扫码机器和微信用户绑定关系
        $this->redis->hSet($adKey . '_scan', $wxOpenId, $machine);

        //第一个用户导粉到联通公众号
        $adWxCodeUrl = null;
        if ($ad = $this->redis->hGet('ad', '10010')) {


            //此广告已存在此粉丝
            if (!$this->redis->hExists($ad . '_user', $wxOpenId)) {

                $adWxCodeUrl = $this->redis->hGet($ad, 'wxCodeUrl');

            }

        }

        $this->redis->close();

        return $this->view->renderSuccess($response, [
            'free' => true,
            'wxCodeUrl' => $adWxCodeUrl
        ]);
    }

    protected function isFree($wxOpenId,$machine){

    }

}