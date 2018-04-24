<?php
namespace RochaMarcelo\CakePimpleDi\Test\TestCase\Event;

use Cake\Http\ActionDispatcher;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\TestSuite\TestCase;
use RochaMarcelo\CakePimpleDi\Di\DiTrait;
use RochaMarcelo\CakePimpleDi\Event\ContainerDispatchListener;

/**
 * Class ContainerDispatchListenerTest
 * @package RochaMarcelo\CakePimpleDi\Test\TestCase\Event
 */
class ContainerDispatchListenerTest extends TestCase
{
    use DiTrait;

    /**
     * Test that the ContainerDispatchEventListener add the request and session objects to the container.
     * @return void
     */
    public function testBeforeDispatchAddsRequestAndSession()
    {
        $response = new Response();
        $dispatcher = new ActionDispatcher();

        $req = new ServerRequest();
        $res = new Response();

        $dispatcher->getEventManager()->on(new ContainerDispatchListener());
        $dispatcher->getEventManager()->on('Dispatcher.beforeDispatch', function () use ($response) {
            return $response;
        });
        $dispatcher->dispatch($req, $res);

        $session = $this->di()->get('session');
        $request = $this->di()->get('request');
        $this->assertInstanceOf(Session::class, $session);
        $this->assertInstanceOf(ServerRequest::class, $request);
    }
}