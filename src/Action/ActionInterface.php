<?php
/**
 * Created by PhpStorm.
 * User: ycy
 * Date: 2/27/18
 * Time: 4:49 PM
 */

namespace Action;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Interface ActionInterface
 *
 */
interface ActionInterface
{
    /**
     * ActionInterface constructor.
     *
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container);

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param array                                    $args
     *
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args);
}