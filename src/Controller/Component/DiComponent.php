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

    private $originalPass;

    /**
     * Called before the controller action. Try to load dependencies
     *
     * @param Event $event An Event instance
     *
     * @return null
     */
    public function startup(Event $event)
    {
        $controller = $this->_registry->getController();
        $request = $controller->getRequest();
        $injections = $this->getConfig('injections');
        $action = $request->getParam('action');

        if ( !isset($injections[$action]) ) {
            return;
        }

        $params = [];
        foreach ($injections[$action] as $dependency ) {
            $params[] = $this->di()->get($dependency);
        }
        $this->originalPass = $request->getParam('pass');
        $pass = array_merge($params, $request->getParam('pass'));
        $controller->setRequest($controller->getRequest()->withParam('pass', $pass));
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     *
     * @return void
     */
    public function beforeRender(Event $event)
    {
        $controller = $this->_registry->getController();
        $injections = $this->getConfig('injections');
        $action = $controller->getRequest()->getParam('action');

        if ( !isset($injections[$action]) ) {
            return;
        }

        $controller->setRequest($controller->getRequest()->withParam('pass', $this->originalPass));
    }
}
