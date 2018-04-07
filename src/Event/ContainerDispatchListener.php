<?php
namespace RochaMarcelo\CakePimpleDi\Event;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Http\ServerRequest;
use RochaMarcelo\CakePimpleDi\Di\DiTrait;

/**
 * Class ContainerDispatchListener
 * @package RochaMarcelo\CakePimpleDi\Event
 */
class ContainerDispatchListener implements EventListenerInterface
{
    use DiTrait;

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Dispatcher.beforeDispatch' => 'containerizeRequest',
        ];
    }

    /**
     * @param Event $event Event object
     * @param ServerRequest $request ServerRequest object
     * @return void
     */
    public function containerizeRequest(Event $event, ServerRequest $request)
    {
        $di = $this->di();
        $di->set('request', $request);
        $di->set('session', $request->getSession());
    }
}
