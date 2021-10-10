<?php
namespace RochaMarcelo\CakePimpleDi\Middleware;

use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use RochaMarcelo\CakePimpleDi\Di\DiTrait;

/**
 * Class ContainerizeRequestMiddleware
 * @package RochaMarcelo\Middleware
 *
 * When loaded this middleware provides a key 'request' and
 * a key for 'session' in the container.
 */
class ContainerizeRequestMiddleware
{
    use DiTrait;

    /**
     * @param ServerRequest $request ServerRequest
     * @param Response $response Response
     * @param callable $next callable
     * @return ResponseInterface
     */
    public function __invoke(ServerRequest $request, Response $response, callable $next)
    {
        $di = $this->di();
        $di->set('request', $request);
        $di->set('session', $request->getSession());

        $response = $next($request, $response);

        return $response;
    }
}
