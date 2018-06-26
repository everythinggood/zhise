<?php
/**
 * Created by PhpStorm.
 * User: ycy
 * Date: 4/3/18
 * Time: 12:17 AM
 */

namespace Action;


use Container\View\JsonView;
use Container\View\RedisKey;
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
        /** @var Response $response */
        $wxOpenId = $request->getParam('wxOpenId');
        $machineCode = $request->getParam('machineCode');
        $tag = $request->getParam('tag');

        if (!$wxOpenId) return $this->view->renderError($response, "wxOpenId is not found!", ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);
        if (!$machineCode) return $this->view->renderError($response, 'machineCode is not found!', ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);
//        if(!$tag) return $this->view->renderError($response,'tag is not found!',ExceptionCode::NAME_INVAIL_VALUE_EXCEPTION);

        $this->logger->addInfo('getCpmAction-request', $request->getParams());

        $url = $this->redis->hGet(RedisKey::KEY_CPM, RedisKey::KEY_CPM_URL);

        $baseUrl = $this->redis->hGet(RedisKey::KEY_CPM, RedisKey::KEY_CPM_BASEURL);

        $this->logger->addInfo("getCpmAction-request", [$wxOpenId, $machineCode, $tag, $url, $baseUrl]);

        $addRequestParam = $this->autoAccountCpm($wxOpenId, $machineCode);

        $machineCode = strtoupper($machineCode);
        if ($this->redis->hExists(RedisKey::KEY_CPM_FILTER, $machineCode)) {
            $url = $this->redis->hGet(RedisKey::KEY_CPM_FILTER, $machineCode);
            return $this->view->renderSuccess($response, [
                'exist' => false,
                'url' => $url,
                'filter' => [$machineCode, $url]
            ]);
        }

        if (!$this->redis->hExists(RedisKey::KEY_CPM, $tag)) {
            return $this->view->renderError($response, "cpm can not set on [$tag]", ExceptionCode::NAME_NOT_EXIST_EXCEPTION, [
                'exist' => false,
                'url' => $baseUrl
            ]);
        }

        $this->logger->addInfo('getCpmAction-response', [$wxOpenId, $machineCode, $tag, $url, $baseUrl, $addRequestParam]);

        $this->redis->close();

        return $this->view->renderSuccess($response, [
            "recordCpm" => $addRequestParam,
            'exist' => true,
            'url' => $url
        ]);
    }

    protected function autoAccountCpm($wxopenid, $machine)
    {
        $listName = "cpm" . (new \DateTime())->format('Y-m-d');
        return $this->redis->lPush($listName, join('_', [$wxopenid, $machine]));
    }

}