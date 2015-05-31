<?php
namespace RochaMarcelo\CakePimpleDi\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;
use RochaMarcelo\CakePimpleDi\Di\DiTrait;

/**
 * Di component
 */
class DiComponent extends Component
{
    use DiTrait;

    /**
     * Called before the controller action. Try to load dependencies
     *
     * @param Event $event An Event instance
     *
     * @return null
     */
    public function beforeFilter(Event $event)
    {
        $controller = $this->_registry->getController();
        $request = $controller->request;
        $injections = $this->config('injections');
        $action = $request->params['action'];

        if ( !isset($injections[$action]) ) {
            return;
        }

        $params = [];
        foreach ($injections[$action] as $dependency ) {
            $params[] = $this->di()->get($dependency);
        }
        $request->params['pass'] = array_merge($params, $request->params['pass']);
    }
}
